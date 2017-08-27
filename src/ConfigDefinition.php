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
		if( isset( $definitions[$name] ) ) {
			$callback = $definitions[$name];
		} else {
			$callback = static::getDefaultDefinitionCallback(
				$name,
				$config
			);
			if( !$callback ) {
				return false;
			}
		}
		if( !is_callable( $callback ) ) {
			return false;
		}
		return call_user_func_array( $callback, [
			\RequestContext::getMain(),
			$config,
			$name,
		]);
	}

	protected static function getDefaultDefinitionCallback( $name, \Config $config = null ) {
		//TODO: make this configurable
		if( is_string( $config->get( $name ) ) ) {
			return "\\BlueSpice\\ConfigDefinition\\StringSetting::getInstance";
		}
		if( is_int( $config->get( $name ) ) ) {
			return "\\BlueSpice\\ConfigDefinition\\IntSetting::getInstance";
		}
		if( is_array( $config->get( $name ) ) ) {
			return "\\BlueSpice\\ConfigDefinition\\ArraySetting::getInstance";
		}
		return null;
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
	 * @return \Message
	 */
	public function getDescripttionMessage() {
		return null;
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
			static::MAIN_PATH_PAKAGE . '/BlueSpice',
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
