<?php

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\RequestContext;

class ConfigDefinitionFactory {

	protected $configDefinitions = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 * @param Config $config
	 * @param ExtensionAttributeBasedRegistry $registry
	 */
	public function __construct( $config, ExtensionAttributeBasedRegistry $registry ) {
		$this->config = $config;
		$this->registry = $registry;
	}

	/**
	 *
	 * @param string $name
	 * @return ConfigDefinition|false
	 */
	public function factory( $name ) {
		if ( empty( $name ) || !$this->config->has( $name ) ) {
			return false;
		}
		$definitions = $this->getConfigDefinitions();
		if ( !isset( $definitions[$name] ) ) {
			return false;
		}
		if ( !is_callable( $definitions[$name] ) ) {
			return false;
		}
		return call_user_func_array( $definitions[$name], [
			RequestContext::getMain(),
			$this->config,
			$name,
		] );
	}

	/**
	 *
	 * @return array
	 */
	public function getRegisteredDefinitions() {
		return array_keys( $this->getConfigDefinitions() );
	}

	/**
	 *
	 * @return array
	 */
	protected function getConfigDefinitions() {
		if ( $this->configDefinitions !== null ) {
			return $this->configDefinitions;
		}
		$this->configDefinitions = [];
		foreach ( $this->registry->getAllKeys() as $key ) {
			$this->configDefinitions[$key] = $this->registry->getValue( $key );
		}
		$this->configDefinitions = array_merge(
			$this->configDefinitions,
			$this->getLegacyConfigDefinitionRegistry()
		);
		return $this->configDefinitions;
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.2 - Use 'BlueSpiceFoundationConfigDefinitionRegistry'
	 * ExtensionAttributeBasedRegistry instead
	 * @return array
	 */
	private function getLegacyConfigDefinitionRegistry() {
		$return = [];
		// TODO: This need to be changed in the future, using globals is not
		// cool! You may implement \BlueSpice\ExtensionManager ;)
		foreach ( $GLOBALS['bsgExtensions'] as $extName => $extDefinition ) {
			if ( empty( $extDefinition['configDefinitions'] ) ) {
				continue;
			}
			$return = array_merge(
				$return,
				$extDefinition['configDefinitions']
			);
		}
		if ( !empty( $return ) ) {
			wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		}
		return $return;
	}

}
