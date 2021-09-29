<?php

namespace BlueSpice\Tag;

use BlueSpice\Tag\MarkerType\General;

abstract class Tag implements ITag {

	/**
	 *
	 * @return bool
	 */
	public function needsDisabledParserCache() {
		return false;
	}

	/**
	 * Returning an empty string will result in no container to be created.
	 * This also means no automatic data attributes will be available.
	 *
	 * @return string
	 */
	public function getContainerElementName() {
		return 'div';
	}

	/**
	 *
	 * @return array
	 */
	public function getResourceLoaderModuleStyles() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public function getResourceLoaderModules() {
		return [];
	}

	/**
	 *
	 * @return bool
	 */
	public function needsParsedInput() {
		return true;
	}

	/**
	 *
	 * @return bool
	 */
	public function needsParseArgs() {
		return true;
	}

	/**
	 *
	 * @return MarkerType
	 */
	public function getMarkerType() {
		return new General();
	}

	/**
	 * @return \BlueSpice\ParamProcessor\IParamDefinition
	 */
	public function getInputDefinition() {
		return null;
	}

	/**
	 * @return \BlueSpice\ParamProcessor\IParamDefinition[]
	 */
	public function getArgsDefinitions() {
		return [];
	}
}
