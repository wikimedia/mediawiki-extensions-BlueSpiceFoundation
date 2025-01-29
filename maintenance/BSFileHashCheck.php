<?php

use MediaWiki\Json\FormatJson;

require_once 'BSMaintenance.php';

class BSFileHashCheck extends BSMaintenance {
	public function __construct() {
		parent::__construct();

		$this->addOption( 'dir', 'The path to the directory to process', true, true );
		$this->addOption(
			'hashes',
			'The JSON file with the CRC checksums to check against',
			true,
			true
		);
		$this->addOption(
			'mode',
			'check|create - whether to check against the directory contents or to create the JSON file',
			false,
			false
		);
	}

	public function execute() {
		$sMode = $this->getOption( 'mode', 'check' );
		$sDir = $this->getOption( 'dir' );
		$sHashes = $this->getOption( 'hashes' );

		if ( $sMode === 'check' ) {
			$this->checkDirectoryContents( $sHashes, $sDir );
		} elseif ( $sMode === 'create' ) {
			$this->createHashFile( $sHashes, $sDir );
		}
	}

	/**
	 *
	 * @param SplFileInfo $oFileInfo
	 * @return string The hash
	 */
	public function getFileHash( $oFileInfo ) {
		return sha1_file( $oFileInfo->getPathname() );
	}

	/**
	 *
	 * @param SplFileInfo $oFileInfo
	 * @param string $sDir
	 * @return string The normalized relative filepath
	 */
	public function getFilePath( $oFileInfo, $sDir ) {
		$sPathName = $oFileInfo->getPathname();
		$sPathName = str_replace( [ '\\\\', '\\' ], '/', $sPathName );
		$sDir = str_replace( [ '\\\\', '\\' ], '/', $sDir );
		$sPathName = preg_replace( '#^' . preg_quote( $sDir ) . '#', '', $sPathName );

		return trim( $sPathName, '/' );
	}

	/**
	 *
	 * @param string $sHashes
	 * @param string $sDir
	 */
	protected function checkDirectoryContents( $sHashes, $sDir ) {
		$aFileHashMap = FormatJson::decode(
			file_get_contents( $sHashes ),
			true
		);

		$aErrors = [];
		foreach ( $aFileHashMap as $sRelPath => $sExpectedHash ) {
			$oFileInfo = new SplFileInfo( $sDir . '/' . $sRelPath );
			$sActualHash = $this->getFileHash( $oFileInfo );
			if ( $sActualHash !== $sExpectedHash ) {
				$aErrors[] = $sRelPath;
			}
		}

		if ( empty( $aErrors ) ) {
			$this->output( 'Code base check OK!' );
		} else {
			$this->output( 'Code base check FAILED! There are changes in the following files:' );
			$this->output( implode( "\n* ", $aErrors ) );
		}
	}

	/**
	 *
	 * @param string $sHashes
	 * @param string $sDir
	 */
	protected function createHashFile( $sHashes, $sDir ) {
		$oIterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $sDir )
		);

		$aFileHashMap = [];
		foreach ( $oIterator as $name => $oFileInfo ) {
			if ( $oFileInfo->isDir() ) {
				continue;
			}

			$sFilePath = $this->getFilePath( $oFileInfo, $sDir );
			if ( substr( $sFilePath, 0, 1 ) === '.' ) {
				continue;
			}
			$sHash = $this->getFileHash( $oFileInfo );

			$aFileHashMap[$sFilePath] = $sHash;
		}

		$this->output( "Saving to $sHashes" );
		file_put_contents( $sHashes, FormatJson::encode( $aFileHashMap, true ) );
	}

}

$maintClass = BSFileHashCheck::class;
require_once RUN_MAINTENANCE_IF_MAIN;
