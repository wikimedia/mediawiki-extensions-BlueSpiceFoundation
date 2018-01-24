<?php

require_once( 'BSMaintenance.php' );

class BSMigrateUserProperties extends LoggedUpdateMaintenance {

	protected $oldData = [];
	protected function readOldData() {
		$res = $this->getDB( DB_REPLICA )->select( 'user_properties', '*' );
		foreach( $res as $row ) {
			if( strpos( $row->up_property, "MW::" ) !== 0 ) {
				continue;
			}
			if( !isset( $this->oldData[$row->up_property] ) ) {
				$this->oldData[$row->up_property] = [];
			}
			$this->oldData[$row->up_property][$row->up_user] = $row->up_value;
		}
	}

	protected $newData = [];
	protected function convertData() {
		foreach( $this->oldData as $oldName => $values ) {
			$newName = $this->makeNewName( $oldName );
			$this->newData[ $newName ] = $values;
		}
	}

	protected function makeNewName( $oldName ) {
		if( $deviatingName = $this->fromDeviatingNames( $oldName ) ) {
			return $deviatingName;
		}

		//MW::SomeExtension::SomeUserProperty
		$nameParts = explode( '::', $oldName );
		array_shift( $nameParts ); //MW
		$newName = implode( '-', $nameParts );
		$newName = strtolower( "bs-$newName" );
		//bs-someextension-someuserproperty

		if( strlen( $newName ) > 255 ) {
			throw new Exception( "Variable name '$newName' is too long!" );
		}

		return $newName;
	}

	protected function fromDeviatingNames( $oldName ) {
		$newName = false;
		\Hooks::run( 'BSMigrateUserPropertiesFromDeviatingNames', [
			$oldName,
			&$newName
		]);
		return $newName;
	}

	protected function saveConvertedData() {
		foreach( $this->newData as $newName => $values ) {
			foreach( $values as $userId => $value ) {
				$row = $this->getDB( DB_REPLICA )->selectRow(
					'user_properties',
					'*',
					[
						'up_property' => $newName,
						'up_user' => $userId,
					],
					__METHOD__
				);
				if( $row ) {
					//this implementation prevents all current testsystems from
					//experiencing problems when certan new user settings
					//already exist
					$this->getDB( DB_MASTER )->update(
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

				$this->getDB( DB_MASTER )->insert(
					'user_properties',
					[
						'up_property' => $newName,
						'up_user' => $userId,
						'up_value' => $value,
					],
					__METHOD__
				);
			}
		}
	}

	protected function doDBUpdates() {

		$this->readOldData();
		$this->convertData();
		$this->saveConvertedData();

		return true;
	}

	protected function getUpdateKey() {
		return 'bs_userproperties-migration';
	}

}
