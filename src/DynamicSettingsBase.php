<?php

namespace BlueSpice;

use Psr\Log\LoggerInterface;
use Status;

abstract class DynamicSettingsBase implements IDynamicSettings {

	/**
	 *
	 * @var LoggerInterface
	 */
	protected $logger = null;

	/**
	 *
	 * @var mixed
	 */
	protected $data = null;

	/**
	 * Standard factory method
	 *
	 * @param LoggerInterface $logger
	 * @return IDynamicSettings
	 */
	public static function factory( $logger ) {
		return new static( $logger );
	}

	/**
	 *
	 * @param LoggerInterface $logger
	 */
	public function __construct( $logger ) {
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function apply( &$globals ) {
		$this->logger->debug( "Applying settings from " . get_class( $this ) );
		$this->doApply( $globals );
	}

	/**
	 *
	 * @param array &$globals
	 * @return void
	 */
	abstract protected function doApply( &$globals );

	/**
	 * @inheritDoc
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * @inheritDoc
	 */
	public function persist() {
		return $this->doPersist();
	}

	/**
	 *
	 * @return Status
	 */
	abstract protected function doPersist();

	/**
	 * @inheritDoc
	 */
	public function fetch() {
		return $this->data;
	}
}
