<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;
use BlueSpice\ConfigDefinition;

class AddBSGConfig extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars = array_merge(
			$this->vars,
			$this->getSettingsToExpose()
		);
		return true;
	}

	protected function getSettingsToExpose() {
		$cfgVars = [];
		$cfgDefFactory = $this->getServices()->getService(
			'BSConfigDefinitionFactory'
		);
		foreach( $cfgDefFactory->getRegisteredDefinitions() as $name ) {
			$cfgDef = $cfgDefFactory->factory( $name );
			if( !$cfgDef instanceof ConfigDefinition ) {
				continue;
			}
			if( !$cfgDef->isRLConfigVar() ) {
				continue;
			}
			$cfgVars[ $cfgDef->getVariableName() ] = $cfgDef->getValue();
		}
		return $cfgVars;
	}
}
