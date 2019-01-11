<?php

namespace BlueSpice;

abstract class ConfigDefinition implements ISetting, ISettingPaths {

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $name
	 */
	public function __construct( $context, $config, $name ) {
		$this->context = $context;
		$this->config = $config;
		$this->name = $name;
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $name
	 * @return ConfigDefinition
	 */
	public static function getInstance( $context, $config, $name ) {
		$callback = static::class;
		$instance = new $callback(
			$context,
			$config,
			$name
		);
		return $instance;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->getConfig()->get( $this->getName() );
	}

	/**
	 *
	 * @return \Config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @return string
	 */
	public function getVariableName() {
		return "bsg" . $this->getName();
	}

	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SYSTEM . '/' . static::EXTENSION_FOUNDATION,
			static::MAIN_PATH_EXTENSION . '/' . static::EXTENSION_FOUNDATION . '/' . static::FEATURE_SYSTEM ,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/' . static::EXTENSION_FOUNDATION,
		];
	}

	/**
	 * Returns if the config is a ResourceLoader variable
	 * @return boolean
	 */
	public function isRLConfigVar() {
		return false;
	}

	/**
	 *
	 * @return string | null
	 */
	public function getHelpMessageKey() {
		return null;
	}

	protected function makeFormFieldParams() {
		return [
			'name' => $this->getName(),
			'fieldname' => $this->getName(),
			'default' => $this->getValue(),
			'id' => $this->makeID(),
			'label-message' => $this->getLabelMessageKey(),
			'parent' => new \HTMLFormEx( [] ),
			'help-message' => $this->getHelpMessageKey(),
		];
	}

	protected function makeID() {
		return $this->getVariableName();
	}

	/**
	 *
	 * @return boolean
	 */
	public function isHidden() {
		return false;
	}
}
