<?php
//icon licence: https://commons.wikimedia.org/wiki/File:Microsoft_Office_2013_logos_lineup.svg

require_once( __DIR__ . '/BSMaintenance.php' );

class AddFileiconsToMediaWiki extends BSMaintenance {
	public function __construct() {
		parent::__construct();
		$this->addOption( 'source', 'source filesystem path to icons', false, true );
		$this->addOption( 'target', 'target filesystem path for icons', false, true );
	}

	/**
	 * Get fileicons and copy to $IP/resources/assets/file-type-icons
	 * @return bool allow other hooked methods to be executed. Always true.
	 */
	public function execute() {
		global $IP;

		if( !( $this->getOption( 'source' ) === null ) ){
			$sSourceDir = $this->getOption( 'source' );
		}
		else{
			$sSourceDir = "$IP/extensions/BlueSpiceFoundation/resources/assets/file-type-icons";
		}


		if( !( $this->getOption( 'target' ) === null ) ){
			$sTargetDir = $this->getOption( 'target' );
		}
		else{
			$sTargetDir = "$IP/resources/assets/file-type-icons";
		}

		$sourceHandler = opendir( $sSourceDir );
		$targetHandler = opendir( $sTargetDir );

		if( $sourceHandler && $targetHandler ){

			$this->output( "\n\ncopy file(s):\n");
			$this->output( "Source: " . $sSourceDir . "" );
			$this->output( "Target: " . $sTargetDir . "\n" );

			while ( ( $fileName = readdir( $sourceHandler ) ) !== false ){
				if( $fileName == "."  || $fileName == ".." ) continue;

				if( file_exists( $sTargetDir . "/" . $fileName ) ){
					$this->output( $fileName . " ... exists" );
					continue;
				}

				if( !copy( $sSourceDir . "/" . $fileName, $sTargetDir . "/" . $fileName ) ){
					$this->output( $fileName . " ... failed" );
					continue;
				}
				else{
					$this->output( $fileName . " ... success" );
				}
			}
		}
		else{
			if( !$sourceHandler ){
				$this->output( "source not valid\n" );
			}
			if( !$targetHandler ){
				$this->output( "target not valid\n" );
			}
		}

		closedir( $targetHandler );
		closedir( $sourceHandler );
	}

}

$maintClass = 'AddFileiconsToMediaWiki';

require_once RUN_MAINTENANCE_IF_MAIN;
