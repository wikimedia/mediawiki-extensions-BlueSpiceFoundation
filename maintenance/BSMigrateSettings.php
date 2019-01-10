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
		$skipSettings = $this->getSkipSettings();

		foreach( $this->oldData as $oldName => $oldValue ) {
			if( in_array( $oldName, $skipSettings ) ) {
				$this->output( "$oldName skipped\n" );
				continue;
			}

			$newName = $this->makeNewName( $oldName );
			$newValue = $this->convertValue( $oldValue );
			$skip = false;
			\Hooks::run( 'BSMigrateSettingsFromDeviatingNames', [
				$oldName,
				&$newName,
				$oldValue,
				&$newValue,
				&$skip,
			] );
			if( $skip === true ) {
				$this->output( "$oldName skipped\n" );
				continue;
			}

			$this->output( "$oldName => $newName\n" );
			$this->newData[ $newName ] = $newValue;
		}
	}

	protected function getSkipSettings() {
		return [
			'MW::DefaultUserImage',
			'MW::DeletedUserImage',
			'MW::AnonUserImage',
			//partially removed packages
			'MW::ExtendedSearch::SolrCore',
			'MW::ExtendedSearch::SolrPingTime',
			'MW::ExtendedSearch::SolrServiceUrl',
			//removed packages
			'MW::TopMenuBarCustomizer::NuberOfLevels',
			'MW::TopMenuBarCustomizer::NumberOfMainEntries',
			'MW::TopMenuBarCustomizer::NumberOfSubEntries',
			'MW::VisualEditor::disableNS',
			'MW::VisualEditor::Use',
			'MW::ShoutBox::AllowArchive',
			'MW::ShoutBox::CommitTimeInterval',
			'MW::ShoutBox::MaxMessageLength',
			'MW::ShoutBox::NumberOfShouts',
			'MW::ShoutBox::Show',
			'MW::ShoutBox::ShowAge',
			'MW::ShoutBox::ShowUser'
		];
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
		return false;
	}

	protected function saveConvertedData() {
		$dbValues = [];
		foreach( $this->newData as $newName => $newValue ) {
			$set = false;
			\Hooks::run( 'BSMigrateSettingsSetNewSettings', [
				$newName,
				$newValue,
				&$set
			] );
			if ( !$set ) {
				// If no other extension did the settings, set it to the db
				$dbValues[] = [
					's_name' => $newName,
					's_value' => $newValue
				];
			}
		}

		$this->getDB( DB_MASTER )->insert( 'bs_settings3', $dbValues );
		\Hooks::run( 'BSMigrateSettingsSaveNewSettings', [ $this->newData ] );
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
