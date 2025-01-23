<?php

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Message\Message;
use MessageLocalizer;

abstract class ConfigDefinition implements ISetting, ISettingPaths, MessageLocalizer {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string $name
	 */
	public function __construct( $context, $config, $name ) {
		$this->context = $context;
		$this->config = $config;
		$this->name = $name;
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
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
	 * @return Config
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

	/**
	 *
	 * @return array
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SYSTEM . '/' . static::EXTENSION_FOUNDATION,
			static::MAIN_PATH_EXTENSION . '/' . static::EXTENSION_FOUNDATION . '/' . static::FEATURE_SYSTEM,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/' . static::EXTENSION_FOUNDATION,
		];
	}

	/**
	 * Returns if the config is a ResourceLoader variable
	 * @return bool
	 */
	public function isRLConfigVar() {
		return false;
	}

	/**
	 *
	 * @return string|null
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
			'parent' => new \HTMLFormEx( [], $this->context ),
			'help-message' => $this->getHelpMessageKey(),
			'disabled' => $this->isDisabled()
		];
	}

	/**
	 *
	 * @return bool
	 */
	protected function isDisabled() {
		return $this->config->getOverrides()->has( $this->getName() );
	}

	protected function makeID() {
		return $this->getVariableName();
	}

	/**
	 *
	 * @return bool
	 */
	public function isHidden() {
		return false;
	}

	/**
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params Normal message parameters
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		return $this->context->msg( $key, ...$params );
	}
}
