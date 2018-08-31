<?php

require_once( __DIR__.'/BSMaintenance.php' );

abstract class BSBatchFileProcessorBase extends BSMaintenance {

	protected $sSrc = '';
	protected $sDest = __DIR__;
	protected $oCurrentFile = null;
	protected $aFiles = array();
	protected $aFileExtensionWhitelist = array();
	protected $aErrors = array();

	public function __construct() {
		parent::__construct();

		$this->addOption('src', 'The path to the source directory', true, true);
		$this->addOption('dest', 'The path to the destination directory', false, true);

		$this->aFileExtensionWhitelist = array_map(
			'strtoupper', $this->aFileExtensionWhitelist
		);
	}

	public function execute() {
		//wfCountDown( 5 );

		$this->sSrc = $this->getOption( 'src', $this->sSrc );
		$this->sDest = $this->getOption( 'dest', $this->sDest );

		$aFiles = $this->getFileList();

		$iProcessedFiles = 0;
		foreach( $aFiles as $sFileName => $oFile ) {
			if( $oFile instanceof SplFileInfo !== true ) {
				$this->error( 'Could not process list item: '
						. $sFileName . ' '
						. var_export( $oFile, true )
				);
				continue;
			}
			$this->output( 'Processing ' . $oFile->getPathname().' ...' );
			$mResult = $this->processFile( $oFile );
			if ( $mResult !== true ) {
				$this->error( '... error:' . $mResult );
			}
			else {
				$this->output( '... done.' );
				$iProcessedFiles++;
			}
		}

		$this->output( $iProcessedFiles.' file(s) processed.' );
		$this->output( count($this->aErrors).' errors(s) occurred.' );
		if( count( $this->aErrors ) > 0 ) {
			$this->output(
				implode( "\n", $this->aErrors )
			);
		}
	}

	/**
	 * Throw an error to the user. Doesn't respect --quiet, so don't use
	 * this for non-error output
	 *
	 * BSMaintenance: Public to allow hook callbacks to write output.
	 * @param $err String: the error to display
	 * @param $die Int: if > 0, go ahead and die out using this int as the code
	 */
	public function error( $err, $die = 0 ) {
		$this->aErrors[] = $err;
		parent::error( $err, $die );
	}

	/**
	 *
	 * @return array\RecursiveIteratorIterator
	 */
	public function getFileList() {
		$sPath = realpath( $this->sSrc );
		$this->output( 'Fetching file list from "'.$sPath.'" ...' );

		$oFiles = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $sPath ),
			RecursiveIteratorIterator::SELF_FIRST
		);

		$aFiles = array();
		foreach ( $oFiles as $sPath => $oFile ) {
			if( $oFile instanceof SplFileInfo === false ) {
				$this->error( 'Not a valid SplFileInfo object: ' . $sPath );
			}
			if( !empty( $this->aFileExtensionWhitelist ) ) {
				$sFileExt = strtoupper( $oFile->getExtension() );
				if( !in_array( $sFileExt, $this->aFileExtensionWhitelist ) ) {
					continue;
				}
			}
			$aFiles[$oFile->getPathname()] = $oFile;
		}

		ksort($aFiles, SORT_NATURAL);
		$this->output( '... found ' . count( $aFiles ) . ' file(s).' );
		return $aFiles;
	}

	/**
	 *
	 * @param SplFileInfo $oFile
	 */
	public abstract function processFile($oFile);
}
