<?php

namespace BlueSpice;

class ConfigDefinitionFactory {

	protected $configDefinitions = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 * @param \Config $config
	 * @return Entity | null
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	/**
	 *
	 * @param string $name
	 * @return ConfigDefinition | false
	 */
	public function factory( $name ) {
		if( empty( $name ) || !$this->config->has( $name ) ) {
			return false;
		}
		$definitions = $this->getConfigDefinitions();
		if( !isset( $definitions[$name] ) ) {
			return false;
		}
		if( !is_callable( $definitions[$name] ) ) {
			return false;
		}
		return call_user_func_array( $definitions[$name], [
			\RequestContext::getMain(),
			$this->config,
			$name,
		]);
	}

	/**
	 *
	 * @return array
	 */
	public function getRegisteredDefinitions() {
		return array_keys( $this->getConfigDefinitions() );
	}

	protected function getConfigDefinitions() {
		if( $this->configDefinitions ) {
			return $this->configDefinitions;
		}
		$this->configDefinitions = [];
		//TODO: This need to be changed in the future, using globals is not
		//cool! You may implement \BlueSpice\ExtensionManager ;)
		foreach( $GLOBALS['bsgExtensions'] as $extName => $extDefinition ) {
			if( empty( $extDefinition['configDefinitions'] ) ) {
				continue;
			}
			$this->configDefinitions = array_merge(
				$this->configDefinitions,
				$extDefinition['configDefinitions']
			);
		}
		return $this->configDefinitions;
	}
}
