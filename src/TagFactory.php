<?php

namespace BlueSpice;

class TagFactory {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @param IRegistry $registry
	 */
	public function __construct( $registry ) {
		$this->registry = $registry;
	}

	/**
	 *
	 * @param string $key
	 * @return Tag\ITag
	 */
	public function get( $key ) {
		$className = $this->registry->getValue( $key );
		if( empty( $className ) ) {
			throw new \Exception( "No class registered for '$key'!" );
		}

		$instance = new $className();
		return $instance;
	}

	/**
	 * @return Tag\ITag[]
	 */
	public function getAll() {
		$registeredTags = $this->registry->getAllKeys();
		$instances = [];

		foreach( $registeredTags as $registeredTag ) {
			$instances[$registeredTag] = $this->get( $registeredTag );
		}

		return $instances;
	}
}
