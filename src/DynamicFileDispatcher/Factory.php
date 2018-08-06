<?php

namespace BlueSpice\DynamicFileDispatcher;

class Factory {
	protected $config = null;

	protected $modules = null;

	/**
	 *
	 * @param type $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	/**
	 *
	 * @param Params $params
	 * @param \IContextSource|null $context
	 * @param boolean $secure - set to false when internal use, to improve
	 * performance
	 * @return Module
	 * @throws \MWException
	 */
	public function newFromParams( Params $params, \IContextSource $context = null, $secure = true ) {
		if( !$context ) {
			$context = \RequestContext::getMain();
		}
		$moduleName = $params->get( Params::MODULE, [
			Params::PARAM_TYPE => Params::TYPE_STRING,
			Params::PARAM_DEFAULT => '',
		]);

		if( empty( $moduleName ) ) {
			throw new \MWException(
				"Param " . Params::MODULE . ": no module name given"
			);
		}
		$moduleClass = $this->getModule( $moduleName );
		if( !$moduleClass ) {
			throw new \MWException(
				"Module '$moduleName' not registered"
			);
		}
		if( !class_exists( $moduleClass ) ) {
			throw new \MWException(
				"Module '$moduleName' class '$moduleClass' not found"
			);
		}

		$instance = new $moduleClass(
			$params,
			$this->config,
			$context,
			$secure
		);

		return $instance;
	}

	protected function getModule( $moduleName ) {
		$modules = $this->getModules();
		if( !isset( $modules[$moduleName] ) ) {
			return false;
		}
		return $modules[$moduleName];
	}

	protected function getModules() {
		if( $this->modules ) {
			return $this->modules;
		}

		$extRegistry = \ExtensionRegistry::getInstance();
		$this->modules = $extRegistry->getAttribute(
			'BlueSpiceFoundationDynamicFileRegistry'
		);
		foreach( $this->modules as $key => $module ) {
			if( !is_array( $module ) ) {
				continue;
			}
			//Attributes get merged together instead of overwritten, so just take the
			//last one
			$this->modules[$key] = end( $module );
		}

		return $this->modules;
	}
}
