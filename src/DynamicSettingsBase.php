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
		if ( $this->shouldApply() ) {
			$this->logger->debug( "Applying settings from " . get_class( $this ) );
			$this->doApply( $globals );
		} else {
			$this->logger->debug( "Skipped applying settings from " . get_class( $this ) );
		}
		if ( !empty( $globals ) ) {
			wfDeprecated(
				__FUNCTION__,
				'4.3',
				get_class( $this ) . ': Passed in `$globals` must not be altered anymore!'
			);
		}
	}

	/**
	 *
	 * @param array &$globals
	 * @return void
	 */
	abstract protected function doApply( &$globals );

	/**
	 * @return bool
	 */
	protected function shouldApply() {
		return true;
	}

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
