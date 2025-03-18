<?php

require_once 'BSMaintenance.php';

use MediaWiki\Json\FormatJson;
use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\MediaWikiServices;

class BSMigrateUserProperties extends LoggedUpdateMaintenance {

	protected $oldData = [];

	protected function readOldData() {
		$res = $this->getDB( DB_REPLICA )->select(
			'user_properties',
			'*',
			'',
			__METHOD__
		);
		foreach ( $res as $row ) {
			if ( strpos( $row->up_property, "MW::" ) !== 0 ) {
				continue;
			}
			if ( !isset( $this->oldData[$row->up_property] ) ) {
				$this->oldData[$row->up_property] = [];
			}
			$this->oldData[$row->up_property][$row->up_user] = $row->up_value;
		}
	}

	protected $newData = [];

	protected function convertData() {
		foreach ( $this->oldData as $oldName => $values ) {
			$newName = $this->makeNewName( $oldName );
			$this->newData[ $newName ] = $values;
		}
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

		// MW::SomeExtension::SomeUserProperty
		$nameParts = explode( '::', $oldName );
		// MW
		array_shift( $nameParts );
		$newName = implode( '-', $nameParts );
		$newName = strtolower( "bs-$newName" );
		// bs-someextension-someuserproperty

		if ( strlen( $newName ) > 255 ) {
			throw new Exception( "Variable name '$newName' is too long!" );
		}

		return $newName;
	}

	/**
	 *
	 * @param string $oldName
	 * @return string|false
	 */
	protected function fromDeviatingNames( $oldName ) {
		$newName = false;
		MediaWikiServices::getInstance()->getHookContainer()->run(
			'BSMigrateUserPropertiesFromDeviatingNames',
			[
				$oldName,
				&$newName
			]
		);
		return $newName;
	}

	/**
	 * Test data:
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::INT', 'i:500;' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::FLOAT', 'd:1.5;' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::BOOL_TRUE', 'b:1;' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::BOOL_FALSE', 'b:0;' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::STRING', 's:5:"hello";' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::ARRAY', 'a:2:{i:0;i:3000;i:1;i:3001;}' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::OBJECT', 'O:8:"stdClass":1:{s:1:"A";i:5;}' );
	 * INSERT INTO user_properties VALUES( 234, 'MW::ABC::NOTSERIALIZED', 'not serialized' );
	 */
	protected function saveConvertedData() {
		foreach ( $this->newData as $newName => $values ) {
			foreach ( $values as $userId => $value ) {
				// Old config mechanism saved values as PHP serialize strings
				// BlueSpiceExtensions/UserPreferences applied unserialize on `UserLoadOptions`
				// We need to revert this:
				// "i:500;" => 500
				// "d:1.5;" => 1.5
				// "b:1;" => 1
				// "s:5:"hello";" => "hello"
				// "a:2:{i:0;i:3000;i:1;i:3001;}" => "3000|3001"
				// "O:8:"stdClass":1:{s:1:"A";i:5;}" => '{"A":5}'
				$newValue = $value;
				$deserializedValue = unserialize( $value );
				if ( $deserializedValue !== false || strpos( $value, 'b:' ) === 0 ) {
					$newValue = $deserializedValue;

					if ( is_array( $deserializedValue ) ) {
						// Better also as JSON?
						$newValue = implode( '|', $deserializedValue );
					}

					if ( is_object( $deserializedValue ) ) {
						$newValue = FormatJson::encode( $deserializedValue );
					}
				}

				$row = $this->getDB( DB_REPLICA )->selectRow(
					'user_properties',
					'*',
					[
						'up_property' => $newName,
						'up_user' => $userId,
					],
					__METHOD__
				);
				if ( $row ) {
					// this implementation prevents all current testsystems from
					// experiencing problems when certain new user settings
					// already exist
					$this->getDB( DB_PRIMARY )->update(
						'user_properties',
						[
							'up_value' => $value,
						],
						[
							'up_property' => $newName,
							'up_user' => $userId,
						],
						__METHOD__
					);
					continue;
				}

				$this->getDB( DB_PRIMARY )->insert(
					'user_properties',
					[
						'up_property' => $newName,
						'up_user' => $userId,
						'up_value' => $newValue,
					],
					__METHOD__
				);
			}
		}
	}

	/**
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
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
		return 'bs_userproperties-migration';
	}

}
