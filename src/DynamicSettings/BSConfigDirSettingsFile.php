<?php

namespace BlueSpice\DynamicSettings;

use BlueSpice\DynamicSettingsBase;
use Status;

abstract class BSConfigDirSettingsFile extends DynamicSettingsBase {

	/**
	 *
	 * @var string
	 */
	protected $data = '';

	/**
	 * @inheritDoc
	 */
	protected function doApply( &$globals ) {
		$path = $this->getPathname();
		include $path;
	}

	/**
	 * Returns the file name
	 *
	 * @return string
	 */
	abstract protected function getFilename();

	/**
	 * Adapted copy of `BlueSpice\PermissionManager\Extension::backupExistingSettings`
	 *
	 * @return void
	 */
	private function backupExistingSettings() {
		$configFile = $this->getPathname();
		$backupFilenamePrefix = $this->makeBackupFilenamePrefix();
		$backupFilenameSuffix = $this->makeBackupFilenameSuffix();

		if ( file_exists( $configFile ) ) {
			$timestamp = wfTimestampNow();
			$backupFilename = "{$backupFilenamePrefix}{$timestamp}.{$backupFilenameSuffix}";
			$backupFile = dirname( $configFile ) . "/{$backupFilename}";

			file_put_contents( $backupFile, file_get_contents( $configFile ) );
		}

		// remove old backup files if max number exceeded
		$configFiles = scandir( dirname( $configFile ) . "/", SCANDIR_SORT_ASCENDING );
		$backupFiles = array_filter(
			$configFiles,
			function ( $elem ) use ( $backupFilenamePrefix ) {
				return ( strpos( $elem, $backupFilenamePrefix ) !== false ) ? true : false;
			}
		);

		// default limit to 5 backups, remove all backup files until "maxbackups" files left
		$maxbackups = $this->getMaxNoOfBackups();
		while ( count( $backupFiles ) > $maxbackups ) {
			$oldBackupFile = dirname( $configFile ) . "/" . array_shift( $backupFiles );
			unlink( $oldBackupFile );
		}
	}

	/**
	 *
	 * @return int
	 */
	protected function getMaxNoOfBackups() {
		return 5;
	}

	/**
	 *
	 * @return string
	 */
	private function makeBackupFilenamePrefix() {
		$parts = explode( '.', $this->getFilename() );
		array_pop( $parts );
		$strippedFilename = implode( '.', $parts );
		$backupFilenamePrefix = "$strippedFilename-backup-";
		return $backupFilenamePrefix;
	}

	/**
	 *
	 * @return string
	 */
	private function makeBackupFilenameSuffix() {
		$parts = explode( '.', $this->getFilename() );
		$fileextension = end( $parts );
		if ( !is_string( $fileextension ) ) {
			$fileextension = 'php';
		}
		return $fileextension;
	}

	/**
	 * @inheritDoc
	 */
	protected function doPersist() {
		$status = Status::newGood();
		$this->backupExistingSettings();
		$res = file_put_contents( $this->getPathname(), $this->data );
		if ( $res === false ) {
			$status->newFatal( "bs-dynamic-settings-error-config-file-could-not-be-written" );
		}
		return $status;
	}

	/**
	 * @inheritDoc
	 */
	public function fetch() {
		$this->data = file_get_contents( $this->getPathname() );
		return parent::fetch();
	}

	/**
	 *
	 * @return string
	 */
	private function getPathname() {
		return BSCONFIGDIR . '/' . $this->getFilename();
	}
}
