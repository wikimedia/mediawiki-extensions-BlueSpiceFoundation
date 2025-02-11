<?php

use MediaWiki\Maintenance\Maintenance;

require_once getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: __DIR__ . '/../../../maintenance/Maintenance.php';

abstract class BSMaintenance extends Maintenance {
	/**
	 *
	 * @var array
	 */
	protected $aOutputBuffer = [];

	public function __construct() {
		parent::__construct();
		$this->addOption(
			'cliOutputTo',
			'Specifiy a file the console output is being written to',
			false,
			true
		);
		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	/**
	 * Throw an error to the user. Doesn't respect --quiet, so don't use
	 * this for non-error output
	 *
	 * BSMaintenance: Public to allow hook callbacks to write output.
	 * @param string $err String: the error to display
	 * @param int $die Int: if > 0, go ahead and die out using this int as the code
	 */
	public function error( $err, $die = 0 ) {
		$this->appendOutputBuffer( $err . "\n", $die );
		parent::error( $err, $die );
	}

	/**
	 * Throw some output to the user. Scripts can call this with no fears,
	 * as we handle all --quiet stuff here
	 *
	 * BSMaintenance: Public to allow hook callbacks to write output.
	 * Adds a line break.
	 * @param string $out String: the text to show to the user
	 * @param mixed|null $channel Mixed: unique identifier for the channel. See
	 *     function outputChanneled.
	 */
	public function output( $out, $channel = null ) {
		// MediaWiki outputs no linebreak in output(), but in error() it does...
		$out .= "\n";
		$this->appendOutputBuffer( $out, 0 );
		parent::output( $out, $channel );
	}

	/**
	 * Adds a line to the output buffer and persists it if the script gets terminated
	 * @param string $out
	 * @param int $die
	 */
	protected function appendOutputBuffer( $out, $die ) {
		$this->aOutputBuffer[] = $out;
		if ( $die !== 0 ) {
			$this->writeOutputBuffer();
		}
	}

	/**
	 * Persists the output to a user specified file
	 */
	protected function writeOutputBuffer() {
		$sOutputFile = $this->getOption( 'cliOutputTo' );
		if ( $sOutputFile !== null ) {
			wfMkdirParents( wfBaseName( realpath( $sOutputFile ) ) );
			file_put_contents( $sOutputFile, implode( "\n", $this->aOutputBuffer ) );
		}
	}
}
