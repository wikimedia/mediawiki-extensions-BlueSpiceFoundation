<?php

namespace BlueSpice;

abstract class StandardTask implements ITask {


	/**
	 *
	 * @var Data\IStore
	 */
	protected $dataStore = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var \LogPage
	 */
	protected $actionLogger = null;

	/**
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger = null;

	/**
	 *
	 * @var \BlueSpice\INotifier
	 */
	protected $notifier = null;

	/**
	 *
	 * @var \stdClass
	 */
	protected $taskData = null;

	/**
	 *
	 * @param stdClass $taskData
	 * @param \IContextSource $context
	 * @param Data\IStore $dataStore
	 * @param ActionLogger|null $actionLogger
	 * @param Notifier|null $notifier
	 * @param \Psr\Log\LoggerInterface|null $logger
	 */
	public function __construct( $taskData, $context, $dataStore, $actionLogger = null, $notifier = null, $logger = null ) {
		$this->taskData = $taskData;

		$this->context = $context instanceof \IContextSource
			? $context
			: \RequestContext::getMain();

		$this->dataStore = $dataStore;

		$this->actionLogger = $actionLogger instanceof ActionLogger
			? $actionLogger
			: $this->makeActionLogger();

		$this->notifier = $notifier instanceof INotifier
			? $notifier
			: NotifierFactory::newNotifier();

		$this->logger = $logger instanceof \Psr\Log\LoggerInterface
			? $logger
			: LoggerFactory::getInstance( self::class );
	}

	/**
	 *
	 * @return \Status;
	 */
	public function execute() {
		try {
			//TODO: Permission checking, Param processing and other stuff
		} catch( \Exception $ex ) {
			return \Status::newFatal( $ex->getMessage() );
		}
		return $this->doExecute();
	}

	/**
	 *
	 * @return \BlueSpice\ActionLogger
	 */
	protected function makeActionLogger() {
		$type = $this->getActionLogType();
		if( empty( $type ) ) {
			return new NullLogger();
		}

		return new ActionLogger(
			$type,
			$this->context->getUser(),
			$this->context->getTitle()
		);
	}

	/**
	 * Allows for auto-creation of a proper ActionLogger to write to Special:Log
	 * @return string
	 */
	protected function getActionLogType() {
		return '';
	}

	/**
	 * @return \Status
	 */
	abstract protected function doExecute();
}
