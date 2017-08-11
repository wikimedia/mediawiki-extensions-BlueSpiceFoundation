<?php

namespace BlueSpice\Tag;

use BlueSpice\Tag\MarkerType\General;

abstract class Tag implements ITag {

	public function needsDisabledParserCache() {
		return false;
	}

	public function getContainerElementName() {
		return 'div';
	}

	public function getResourceLoaderModuleStyles() {
		return [];
	}

	public function getResourceLoaderModules() {
		return [];
	}

	public function needsParsedInput() {
		return true;
	}

	public function needsParseArgs() {
		return true;
	}

	public function getMarkerType() {
		return new General();
	}

	public function getInputDefinition() {
		return null;
	}

	public function getArgsDefinitions() {
		return [];
	}
}
