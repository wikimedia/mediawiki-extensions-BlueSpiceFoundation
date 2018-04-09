<?php

namespace BlueSpice\RunJobsTriggerHandler;

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
	 * @var array
	 */
	protected $data = [];

	/**
	 *
	 * @param \DateTime $currentRunTimestamp
	 * @param string $fileSavePath
	 */
	public function __construct( $currentRunTimestamp, $fileSavePath, $logger ) {
		$this->currentRunTimestamp = $currentRunTimestamp;
		$this->fileSavePath = $fileSavePath;
		$this->logger = $logger;

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
	 * @return boolean
	 */
	public function shouldRun( $runJobsTriggerHandler, $regKey ) {
		$savedNextTS = $this->getSavedNextTimestamp( $regKey );
		if( $this->currentRunTimestamp < $savedNextTS ) {
			return false;
		}

		$newNextTS = $runJobsTriggerHandler
			->getInterval()
			->getNextTimestamp( $this->currentRunTimestamp );

		//TODO: Check 'runJobs.php' execution frequency (by convention
		//"15 minutes") against '$newNextTS'. If fequency is to low to fullfill
		//requested clock, emit error to LoggerInterface

		$this->saveNewNextTimestamp( $regKey, $newNextTS );

		return true;
	}

	protected function loadPersistenceFile() {
		$this->data = \FormatJson::decode (
			file_get_contents( $this->getPersistenceFilepath() ),
			true
		);

		if( !is_array( $this->data ) ) {
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

	protected function getPersistenceFilepath() {
		return $this->fileSavePath . '/runJobsTriggerData.json';
	}

	protected function getSavedNextTimestamp( $regKey ) {
		if( !isset( $this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ] ) ) {
			$dummyTS = new \DateTime();
			$dummyTS->modify( '-1 hour' ); //Set to past, so handler runs
			return $dummyTS;
		}

		$unixTS = wfTimestamp(
			TS_UNIX,
			$this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ]
		);

		$savedTS = new \DateTime();
		$savedTS->setTimestamp( $unixTS );

		return $savedTS;
	}

	protected function saveNewNextTimestamp( $regKey, $newNextTS ) {
		$this->data[ static::DATA_KEY_NEXTRUNS ][ $regKey ] =
			wfTimestamp( TS_MW, $newNextTS );
	}
}
