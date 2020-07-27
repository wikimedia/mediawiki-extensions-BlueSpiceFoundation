<?php

require_once 'BSMaintenance.php';

use MediaWiki\MediaWikiServices;

class BSMigrateConfigFiles extends LoggedUpdateMaintenance {

	private $pattern = '#\$(wg[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#is';
	private $replacement = '$GLOBALS[\'$1\']';

	/**
	 *
	 * @return bool
	 */
	protected function noDataToMigrate() {
		return !$this->getConfig()->has( 'ConfigFiles' )
			|| empty( $this->getConfig()->get( 'ConfigFiles' ) );
	}

	/**
	 * @return Config
	 */
	public function getConfig() {
		return MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
	}

	/**
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		if ( $this->noDataToMigrate() ) {
			$this->output( "{$this->getUpdateKey()}: No data to migrate" );
			return true;
		}
		$this->output( "{$this->getUpdateKey()}: Migrate config files\n" );
		foreach ( $this->getConfig()->get( 'ConfigFiles' ) as $key => $filename ) {
			$this->output( " * $filename\n   => " );
			if ( !file_exists( $filename ) ) {
				$this->output( "does not exist\n" );
				continue;
			}
			$content = file_get_contents( $filename );
			if ( empty( $content ) ) {
				$this->output( "empty\n" );
				continue;
			}
			$this->output( "replacing..." );
			$count = 0;
			$newContent = preg_replace(
				$this->pattern,
				$this->replacement,
				$content,
				-1,
				$count
			);
			if ( empty( $newContent ) ) {
				$this->output( "FAILED!\n" );
				continue;
			}
			if ( $count < 1 ) {
				$this->output( "$count SKIP\n" );
				continue;
			}
			$this->output( "$count backup..." );
			$res = file_put_contents(
				"$filename.MigrateConfigFiles.backup.php",
				$content
			);
			if ( !$res ) {
				$this->output( "FAILED!\n" );
				continue;
			}
			$this->output( " saving..." );
			$res = file_put_contents( $filename, $newContent );
			if ( !$res ) {
				$this->output( "FAILED!\n" );
				continue;
			}
			$this->output( "OK\n" );
		}
		return true;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'bs_configfiles-migration';
	}

}
