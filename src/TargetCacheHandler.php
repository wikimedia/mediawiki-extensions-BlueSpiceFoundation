<?php

namespace BlueSpice;

use BlueSpice\TargetCache\ITarget;
use BlueSpice\Utility\CacheHelper;

class TargetCacheHandler implements ITargetCacheHandler {

	/**
	 *
	 * @var CacheHelper
	 */
	protected $cacheHelper = null;

	/**
	 *
	 * @var ITarget
	 */
	protected $target = null;

	/**
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 *
	 * @var string
	 */
	protected $targetType = '';

	/**
	 *
	 * @var mixed
	 */
	protected $data = false;

	/**
	 *
	 * @param string $targetType
	 * @param string $type
	 * @param CacheHelper $cacheHelper
	 * @param ITarget $target
	 */
	public function __construct( $targetType, $type, CacheHelper $cacheHelper, ITarget $target ) {
		$this->targetType = $targetType;
		$this->type = $type;
		$this->target = $target;
		$this->cacheHelper = $cacheHelper;
	}

	/**
	 * @return string
	 */
	protected function getCacheKey() {
		return $this->cacheHelper->getCacheKey(
			'BlueSpice',
			'TargetCache',
			$this->targetType,
			$this->type,
			$this->target->getIdentifier()
		);
	}

	/**
	 *
	 * @return mixed
	 */
	public function get() {
		if ( $this->data !== false ) {
			return $this->data;
		}
		$this->data = $this->cacheHelper->get( $this->getCacheKey() );
		return $this->data;
	}

	/**
	 *
	 * @param mixed $data
	 */
	public function set( $data ) {
		$this->cacheHelper->set(
			$this->getCacheKey(),
			$data,
			$this->getExipryTime()
		);
	}

	/**
	 *
	 * @param string $action
	 * @return bool
	 */
	public function invalidate( $action = '' ) {
		return $this->cacheHelper->invalidate( $this->getCacheKey() );
	}

	/**
	 * @return int
	 */
	protected function getExipryTime() {
		return 60 * 60 * 24;
	}
}
