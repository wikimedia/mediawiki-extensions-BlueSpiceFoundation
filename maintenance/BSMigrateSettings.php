<?php

require_once( 'BSMaintenance.php' );

class BSMigrateSettings extends LoggedUpdateMaintenance {

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
		if( $deviatingName = $this->fromDeviatingNames( $oldName ) ) {
			return $deviatingName;
		}

		//$oldName = "MW::TopMenuBarCustomizer::NumberOfSubEntries"
		$nameParts = explode( '::', $oldName );
		array_shift( $nameParts ); //MW
		$newName = implode( '', $nameParts );

		if( strlen( $newName ) > 255 ) {
			throw new Exception( "Variable name '$newName' is too long!" );
		}

		return $newName;
	}

	protected function fromDeviatingNames( $oldName ) {
		if( $oldName === 'MW::LogoPath' ) {
			return 'Logo';
		}
		if( $oldName === 'MW::FaviconPath' ) {
			return 'Favicon';
		}
		$newName = false;
		\Hooks::run( 'BSMigrateSettingsFromDeviatingNames', [
			$oldName,
			&$newName
		]);
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

	protected function doDBUpdates() {
		if( $this->noDataToMigrate() ) {
			$this->output( "bs_settings -> bs_settings3: No data to migrate" );
			return true;
		}

		$this->readOldData();
		$this->convertData();
		$this->saveConvertedData();

		return true;
	}

	protected function getUpdateKey() {
		return 'bs_settings3-migration';
	}

}
