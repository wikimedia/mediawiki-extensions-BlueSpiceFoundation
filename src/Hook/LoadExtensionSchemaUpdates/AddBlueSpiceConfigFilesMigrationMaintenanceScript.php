<?php

namespace BlueSpice\Hook\LoadExtensionSchemaUpdates;

use BlueSpice\Hook\LoadExtensionSchemaUpdates;

class AddBlueSpiceConfigFilesMigrationMaintenanceScript extends LoadExtensionSchemaUpdates {
	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->updater->addPostDatabaseUpdateMaintenance( \BSMigrateConfigFiles::class );
		return true;
	}

}
