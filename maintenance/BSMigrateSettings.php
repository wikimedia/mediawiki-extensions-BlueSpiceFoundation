<?php

require_once( 'BSMaintenance.php' );

class BSMigrateSettings extends Maintenance {

	public function execute() {
		if( $this->noDataToMigrate() ) {
			$this->output( "bs_settings -> bs_settings3: No data to migrate" );
			return;
		}

		$this->readOldData();
		$this->convertData();
		$this->saveConvertedData();
	}

	protected function noDataToMigrate() {
		return $this->getDB( DB_REPLICA )->tableExists( 'bs_settings' ) === false;
	}

	protected $oldData = [];
	protected function readOldData() {
		$res = $this->getDB( DB_REPLICA )->select( 'bs_settings', '*' );
		foreach( $res as $row ) {
			$this->oldData[$row->key] = $row->value;
		}
	}

	protected $newData = [];
	protected function convertData() {
		foreach( $this->oldData as $oldName => $serializedValue ) {
			$newName = $this->makeNewName( $oldName );
			$newValue = $this->convertValue( $serializedValue );
			$this->output( "$oldName => $newName\n" );
			$this->newData[ $newName ] = $newValue;
		}
	}

	protected function makeNewName( $oldName ) {
		if( $oldName === 'MW::LogoPath' ) {
			return 'wgLogo';
		}

		//$oldName = "MW::TopMenuBarCustomizer::NumberOfSubEntries"
		$nameParts = explode( '::', $oldName );
		array_shift( $nameParts ); //MW
		$newName = 'bsg' . implode( '', $nameParts );

		if( strlen( $newName ) > 255 ) {
			throw new Exception( "Variable name '$newName' is too long!" );
		}

		return $newName;
	}

	protected function saveConvertedData() {
		foreach( $this->newData as $newName => $newValue ) {
			$this->getDB( DB_MASTER )->insert( 'bs_settings3', [
				's_name' => $newName,
				's_value' => $newValue
			] );
		}
	}

	protected function convertValue( $serializedValue ) {
		$newValue = unserialize( $serializedValue );
		/*if( is_int(  $newValue ) ) {
			$newValue = (int) $newValue;
		}
		if( is_array( $newValue ) ) {
			$newArray = [];
			foreach( $newValue as $key => $element ) {
				$newArray[$key] = $this->convertValue( $element );
			}
			$newValue = $newArray;
		}
		if( is_bool( $newValue ) ) {
			$newValue = (bool) $newValue;
		}*/

		return FormatJson::encode( $newValue );
	}

}

$maintClass = 'BSMigrateSettings';
if (defined('RUN_MAINTENANCE_IF_AIN')) {
	require_once( RUN_MAINTENANCE_IF_MAIN );
} else {
	require_once( DO_MAINTENANCE ); # Make this work on versions before 1.17
}