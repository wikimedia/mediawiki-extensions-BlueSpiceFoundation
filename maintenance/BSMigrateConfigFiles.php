<?php

require_once 'BSMaintenance.php';

use BlueSpice\DynamicSettingsManager;

class BSMigrateConfigFiles extends LoggedUpdateMaintenance {

	private $pattern = '#\$((wg|bsg)([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))#is';
	private $removeLinePattern = '#(global .*?\;[\n|\r\n]?)#m';
	private $replacement = '$GLOBALS[\'$1\']';

	/**
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		$manager = DynamicSettingsManager::factory();
		try {
			$fileKeys = $manager->getAllKeys();
		} catch ( Exception $ex ) {
			$this->output( $ex->getMessage() . PHP_EOL );
			return false;
		}
		if ( empty( $fileKeys ) ) {
			$this->output( "{$this->getUpdateKey()}: No data to migrate" );
			return true;
		}
		$this->output( "{$this->getUpdateKey()}: Migrate config files\n" );
		foreach ( $fileKeys as $key ) {
			$content = $manager->fetch( $key );
			if ( !$content ) {
				$this->output( "Content for $key is empty. Skipping" . PHP_EOL );
				continue;
			}

			$this->output( "Converting $key..." );
			$count = $removeCount = 0;
			$newContent = preg_replace(
				$this->removeLinePattern,
				'',
				$content,
				-1,
				$removeCount
			);
			$newContent = preg_replace(
				$this->pattern,
				$this->replacement,
				$newContent,
				-1,
				$count
			);
			if ( empty( $newContent ) ) {
				$this->output( "FAILED!" . PHP_EOL );
				continue;
			}
			$count += $removeCount;
			if ( $count < 1 ) {
				$this->output( "Nothing to replace, skipping" . PHP_EOL );
				continue;
			}

			$status = $manager->persist( $key, $newContent );
			if ( $status->isOK() ) {
				$this->output( 'done' . PHP_EOL );
			} else {
				$this->output( "FAILED!" . PHP_EOL );
			}
		}

		return true;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'bs_configfiles-migration';
	}
}

$maintClass = BSMigrateConfigFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
