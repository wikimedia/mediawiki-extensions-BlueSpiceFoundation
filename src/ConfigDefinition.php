<?php

namespace BlueSpice;

abstract class ConfigDefinition implements ISetting {

	protected static $configDefinitions = null;

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
	 * @param \Context $context
	 * @param \Config $config
	 * @param string $name
	 */
	protected function __construct( $context, $config, $name ) {
		$this->context = $context;
		$this->config = $config;
		$this->name = $name;
	}

	/**
	 *
	 * @param string $name
	 * @param \Config $config
	 * @return ConfigDefinition | false
	 */
	public static function factory( $name, \Config $config = null ) {
		if( !$config ) {
			$config = \MediaWiki\MediaWikiServices::getInstance()
				->getConfigFactory()->makeConfig( 'bsg' );
		}
		if( empty( $name ) || !$config->has( $name ) ) {
			return false;
		}
		$definitions = static::getConfigDefinitions();
		if( !isset( $definitions[$name] ) ) {
			return false;
		}
		if( !is_callable( $definitions[$name] ) ) {
			return false;
		}
		return call_user_func_array( $definitions[$name], [
			\RequestContext::getMain(),
			$config,
			$name,
		]);
	}

	protected static function getConfigDefinitions() {
		if( static::$configDefinitions ) {
			return static::$configDefinitions;
		}
		static::$configDefinitions = [];
		foreach( $GLOBALS['bsgExtensions'] as $extName => $extDefinition ) {
			if( empty( $extDefinition['configDefinitions'] ) ) {
				continue;
			}
			static::$configDefinitions = array_merge(
				static::$configDefinitions,
				$extDefinition['configDefinitions']
			);
		}
		return static::$configDefinitions;
	}

	/**
	 *
	 * @param \Context $context
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
			static::MAIN_PATH_TYPE . '/' . static::TYPE_SYSTEM,
			static::MAIN_PATH_EXTENSION . '/BlueSpiceFoundation',
			static::MAIN_PATH_PACKAGE . '/BlueSpice',
		];
	}

	public function isStored() {
		return false;
	}

	protected function makeFormFieldParams() {
		return [
			'name' => $this->getName(),
			'fieldname' => $this->getName(),
			'default' => $this->getValue(),
			'id' => $this->makeID(),
			'label-message' => $this->getLabelMessageKey(),
			'disabled' => !$this->isStored(),
		];
	}

	protected function makeID() {
		return $this->getVariableName();
	}
}
