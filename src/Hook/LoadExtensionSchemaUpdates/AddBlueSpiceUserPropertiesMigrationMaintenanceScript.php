<?php

namespace BlueSpice\Hook\LoadExtensionSchemaUpdates;

use BlueSpice\Hook\LoadExtensionSchemaUpdates;

class AddBlueSpiceUserPropertiesMigrationMaintenanceScript extends LoadExtensionSchemaUpdates {
	protected function doProcess() {

		$this->updater->addPostDatabaseUpdateMaintenance(
			'BSMigrateUserProperties'
		);
		return true;
	}

	protected function getExtensionPath() {
		return dirname( dirname( dirname( __DIR__ ) ) );
	}

}
