<?php

namespace BlueSpice\RunJobsTriggerHandler;

use \Config;

class JSONFileBasedRunConditionChecker implements IRunConditionChecker {

	const DATA_KEY_LASTRUN = 'lastrun';
	const DATA_KEY_NEXTRUNS = 'nextruns';

	/**
	 *
	 * @var \DateTime
	 */
	protected $currentRunTimestamp = null;

	/**
	 *
	 * @var string
	 */
	protected $fileSavePath = '';

	/**
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @param string $fileSavePath
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param Config $config
	 */
	public function __construct( $currentRunTimestamp, $fileSavePath, $logger, Config $config ) {
		$this->currentRunTimestamp = $currentRunTimestamp;
		$this->fileSavePath = $fileSavePath;
		$this->logger = $logger;
		$this->config = $config;

		$this->loadPersistenceFile();
	}

	public function __destruct() {
		$this->data[ static::DATA_KEY_LASTRUN ] =
			wfTimestamp( TS_MW, $this->currentRunTimestamp );

		$this->savePersistenceFile();
	}

	/**
	 *
	 * @param \BlueSpice\RunJobsTriggerHandler $runJobsTriggerHandler
	 * @param string $regKey
	 * @return bool
	 */
	public function shouldRun( $runJobsTriggerHandler, $regKey ) {
		$savedNextTS = $this->getSavedNextTimestamp( $regKey );
		if ( $this->currentRunTimestamp < $savedNextTS ) {
			return false;
		}

		$options = $this->makeOptions( $regKey );
		$newNextTS = $runJobsTriggerHandler
			->getInterval()
			->getNextTimestamp(
				$this->currentRunTimestamp,
				$options
		);

		// TODO: Check 'runJobs.php' execution frequency (by convention
		// "15 minutes") against '$newNextTS'. If fequency is to low to fullfill
		// requested clock, emit error to LoggerInterface

		$this->saveNewNextTimestamp( $regKey, $newNextTS );

		return true;
	}

	/**
	 *
	 * @param string $regKey
	 * @return array
	 */
	protected function makeOptions( $regKey ) {
		$options = (array)$this->config->get( 'RunJobsTriggerHandlerOptions' );
		$handlerSpecificOptions = $options['*'];
		if ( isset( $options[$regKey] ) ) {
			$handlerSpecificOptions = array_merge(
				$handlerSpecificOptions,
				$options[$regKey]
			);
		}
		return $handlerSpecificOptions;
	}

	protected function loadPersistenceFile() {
		if ( file_exists( $this->getPersistenceFilepath() ) ) {
			$this->data = \FormatJson::decode(
				file_get_contents( $this->getPersistenceFilepath() ),
				true
			);
		}

		if ( !is_array( $this->data ) || empty( $this->data ) ) {
			$this->data = [
				static::DATA_KEY_LASTRUN => '',
				static::DATA_KEY_NEXTRUNS => []
			];
		}
	}

	protected function savePersistenceFile() {
		file_put_contents(
			$this->getPersistenceFilepath(),
			\FormatJson::encode( $this->data, true )
		);
	}

	/**
	 *
	 * @return string
	 */
	protected function getPersistenceFilepath() {
		return $this->fileSavePath . '/runJobsTriggerData.json';
	}

	/**
	 *
	 * @param string $regKey
	 * @return \DateTime
	 */
	protected function getSavedNextTimestamp( $regKey ) {
		if ( !isset( $this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ] ) ) {
			$dummyTS = new \DateTime();
			// Set to past, so handler runs
			$dummyTS->modify( '-1 hour' );
			return $dummyTS;
		}

		$mediawikiTS = $this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ];
		$unixTS = wfTimestamp( TS_UNIX, $mediawikiTS );

		$savedTS = new \DateTime();
		$savedTS->setTimestamp( $unixTS );

		return $savedTS;
	}

	/**
	 *
	 * @param string $regKey
	 * @param string $newNextTS
	 */
	protected function saveNewNextTimestamp( $regKey, $newNextTS ) {
		$this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ] =
			wfTimestamp( TS_MW, $newNextTS );
	}
}
