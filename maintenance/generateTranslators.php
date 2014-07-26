<?php

/**
 * @author Stephan Muggli
 */

//We are on <mediawiki>/extensions/BlueSpiceExtensions/ExtendedSearch/maintenance
$IP = realpath( dirname( dirname( dirname( __DIR__ ) ) ) );

require_once( $IP.'/extensions/BlueSpiceFoundation/maintenance/BSMaintenance.php' );

class generateTranslators extends BSMaintenance {

	private $aTranslators = array();

	public function execute() {
		global $IP;
		$aPaths = array(
			$IP . '/extensions/BlueSpiceExtensions/',
			$IP . '/extensions/BlueSpiceFoundation/',
			$IP . '/skins/BlueSpiceSkin/'
		);

		foreach( $aPaths as $sPath ) {
			$this->readInFiles( $sPath );
		}
		$this->aTranslators['translators'] = array_map( 'trim', $this->aTranslators['translators'] );
		$this->aTranslators['translators'] = array_unique( $this->aTranslators['translators'] );
		asort( $this->aTranslators['translators'] );
		$this->aTranslators['ts'] = wfTimestampNow();

		touch( $IP . '/extensions/BlueSpiceFoundation/includes/specials/translators.json' );
		$vData = json_encode( $this->aTranslators );
		file_put_contents( $IP . '/extensions/BlueSpiceFoundation/includes/specials/translators.json', $vData );
	}

	public function readInFiles( $sDir ) {
		$oCurrentDirectory = new DirectoryIterator( $sDir );
		foreach ( $oCurrentDirectory as $oFileinfo ) {
			if ( $oFileinfo->isFile() && strpos( $oFileinfo->getFilename(), '.json' ) !== false ) {
				$sContent = json_decode(
					file_get_contents( $oFileinfo->getPath() .DS. $oFileinfo->getFilename() )
				);
				foreach ( $sContent as $aData ) {
					if ( $aData instanceof StdClass && isset( $aData->authors ) ) {
						foreach ( $aData->authors as $Author ) {
							$this->aTranslators['translators'][] = $Author;
						}
					}
				}
				continue;
			}
			if ( $oFileinfo->isDir() && !$oFileinfo->isDot() && $oFileinfo->getFilename() != $sDir ) {
				$this->readInFiles( $oFileinfo->getPath() .DS. $oFileinfo->getFilename() );
			}
		}
	}
}

$maintClass = 'generateTranslators';
if (defined('RUN_MAINTENANCE_IF_MAIN')) {
	require_once( RUN_MAINTENANCE_IF_MAIN );
} else {
	require_once( DO_MAINTENANCE ); # Make this work on versions before 1.17
}