<?php

require_once 'BSMaintenance.php';

use MediaWiki\Json\FormatJson;
use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBQueryError;

class BSMigrateSettings extends LoggedUpdateMaintenance {

	/**
	 *
	 * @return bool
	 */
	protected function noDataToMigrate() {
		return $this->getDB( DB_REPLICA )->tableExists( 'bs_settings', __METHOD__ ) === false;
	}

	/**
	 *
	 * @var array
	 */
	protected $oldData = [];

	protected function readOldData() {
		$res = $this->getDB( DB_REPLICA )->select(
			'bs_settings',
			'*',
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->oldData[$row->key] = $row->value;
		}
	}

	/**
	 *
	 * @var array
	 */
	protected $newData = [];

	protected function convertData() {
		$skipSettings = $this->getSkipSettings();

		foreach ( $this->oldData as $oldName => $oldValue ) {
			if ( in_array( $oldName, $skipSettings ) ) {
				$this->output( "$oldName skipped\n" );
				continue;
			}

			$newName = $this->makeNewName( $oldName );
			$newValue = $this->convertValue( $oldValue );
			$skip = false;
			MediaWikiServices::getInstance()->getHookContainer()->run(
				'BSMigrateSettingsFromDeviatingNames',
				[
					$oldName,
					&$newName,
					$oldValue,
					&$newValue,
					&$skip,
				]
			);
			if ( $skip === true ) {
				$this->output( "$oldName skipped\n" );
				continue;
			}

			$this->output( "$oldName => $newName\n" );
			$this->newData[ $newName ] = $newValue;
		}
	}

	/**
	 *
	 * @return array
	 */
	protected function getSkipSettings() {
		return [
			'MW::DefaultUserImage',
			'MW::DeletedUserImage',
			'MW::AnonUserImage',
			// partially removed packages
			'MW::ExtendedSearch::SolrCore',
			'MW::ExtendedSearch::SolrPingTime',
			'MW::ExtendedSearch::SolrServiceUrl',
			// removed packages
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

	/**
	 *
	 * @param string $oldName
	 * @return string
	 * @throws Exception
	 */
	protected function makeNewName( $oldName ) {
		$deviatingName = $this->fromDeviatingNames( $oldName );
		if ( $deviatingName ) {
			return $deviatingName;
		}

		// $oldName = "MW::TopMenuBarCustomizer::NumberOfSubEntries"
		$nameParts = explode( '::', $oldName );
		// MW
		array_shift( $nameParts );
		$newName = implode( '', $nameParts );

		if ( strlen( $newName ) > 255 ) {
			throw new Exception( "Variable name '$newName' is too long!" );
		}

		return $newName;
	}

	/**
	 *
	 * @param string $oldName
	 * @return bool|string
	 */
	protected function fromDeviatingNames( $oldName ) {
		if ( $oldName === 'MW::LogoPath' ) {
			return 'Logo';
		}
		if ( $oldName === 'MW::FaviconPath' ) {
			return 'Favicon';
		}
		return false;
	}

	protected function saveConvertedData() {
		$dbValues = [];
		foreach ( $this->newData as $newName => $newValue ) {
			$set = false;
			MediaWikiServices::getInstance()->getHookContainer()->run(
				'BSMigrateSettingsSetNewSettings',
				[
					$newName,
					$newValue,
					&$set
				]
			);
			if ( !$set ) {
				// If no other extension did the settings, set it to the db
				$dbValues[] = [
					's_name' => $newName,
					's_value' => $newValue
				];
			}
		}

		try {
			$this->getDB( DB_PRIMARY )->insert(
				'bs_settings3',
				$dbValues,
				__METHOD__
			);
			MediaWikiServices::getInstance()->getHookContainer()->run(
				'BSMigrateSettingsSaveNewSettings',
				[
					$this->newData
				]
			);
		} catch ( DBQueryError $ex ) {
			$this->output( "bs_settings -> bs_settings3: {$ex->getMessage()}" );
		}
	}

	/**
	 *
	 * @param string $serializedValue
	 * @return string
	 */
	protected function convertValue( $serializedValue ) {
		$newValue = unserialize( $serializedValue );

		return FormatJson::encode( $newValue );
	}

	/**
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		if ( $this->noDataToMigrate() ) {
			$this->output( "bs_settings -> bs_settings3: No data to migrate" );
			return true;
		}

		$this->readOldData();
		$this->convertData();
		$this->saveConvertedData();
		return true;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'bs_settings3-migration';
	}

}
