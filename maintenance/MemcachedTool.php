<?php

require_once dirname( dirname( dirname( __DIR__ ) ) ) . "/maintenance/Maintenance.php";

class MemcachedTool extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Tool to manage Memcached' );
		$this->addArg( 'command', 'Command to run: "flush" or "stats"', true );
	}

	public function execute() {
		$command = $this->getArg( 0 );
		if ( !in_array( $command, [ 'flush', 'stats' ], true ) ) {
			$this->fatalError( 'Unknown command "' . $command . '". Use "flush" or "stats".' );
		}

		$factory = MediaWiki\MediaWikiServices::getInstance()->getObjectCacheFactory();
		$cache = $factory->getInstance( CACHE_MEMCACHED );

		if ( !( $cache instanceof MemcachedBagOStuff ) ) {
			$this->fatalError(
				'No Memcached cache is configured in this application (got: ' . get_class( $cache ) . ').'
			);
		}

		$ref = new ReflectionProperty( $cache, 'client' );
		$ref->setAccessible( true );
		$client = $ref->getValue( $cache );

		if ( extension_loaded( 'memcached' ) && $client instanceof Memcached ) {
			// PECL Memcached extension
			if ( $command === 'flush' ) {
				$this->flushPecl( $client );
			} else {
				$this->statsPecl( $client );
			}
		} else {
			// Native MediaWiki MemcachedClient (pure PHP)
			if ( $command === 'flush' ) {
				$this->flushNative( $client );
			} else {
				$this->statsNative( $client );
			}
		}
	}

	private function flushPecl( Memcached $client ): void {
		if ( $client->flush() ) {
			$this->output( "Cache flushed successfully.\n" );
		} else {
			$this->fatalError( 'Failed to flush cache: ' . $client->getResultMessage() );
		}
	}

	private function statsPecl( Memcached $client ): void {
		$stats = $client->getStats();
		if ( $stats === false ) {
			$this->fatalError( 'Failed to retrieve stats: ' . $client->getResultMessage() );
		}
		foreach ( $stats as $server => $serverStats ) {
			$this->output( "=== $server ===\n" );
			foreach ( $serverStats as $key => $value ) {
				$this->output( "STAT $key $value\n" );
			}
			$this->output( "\n" );
		}
	}

	private function flushNative( $client ): void {
		foreach ( $client->_servers as $server ) {
			$sock = $client->sock_to_host( $server );
			if ( !$sock ) {
				$this->output( "Cannot connect to $server\n" );
				continue;
			}
			fwrite( $sock, "flush_all\r\n" );
			$response = fgets( $sock );
			$this->output( "$server: " . trim( $response ) . "\n" );
		}
	}

	private function statsNative( $client ): void {
		foreach ( $client->_servers as $server ) {
			$sock = $client->sock_to_host( $server );
			if ( !$sock ) {
				$this->output( "Cannot connect to $server\n" );
				continue;
			}
			fwrite( $sock, "stats\r\n" );
			$stats = '';
			while ( ( $line = fgets( $sock ) ) !== false ) {
				if ( trim( $line ) === 'END' ) {
					break;
				}
				$stats .= $line;
			}
			$this->output( "=== $server ===\n$stats\n" );
		}
	}
}

$maintClass = MemcachedTool::class;
require_once RUN_MAINTENANCE_IF_MAIN;
