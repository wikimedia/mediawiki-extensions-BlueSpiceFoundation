<?php

namespace BlueSpice;

class SettingPathFactory implements ISettingPaths {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IRegistry $registry
	 * @param \Config $config
	 */
	public function __construct( $registry, $config ) {
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 *
	 * @param string $key
	 * @return string | false
	 */
	public function getMessageKey( $key ) {
		return $this->registry->getValue(
			$key,
			false
		);
	}
}
