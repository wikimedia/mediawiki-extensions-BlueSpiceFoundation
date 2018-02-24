<?php

namespace BlueSpice;

interface ITagExtensionDefinitionProvider {

	/**
	 * Returns an array of tag extension definitions
	 * @return array
	 */
	public function makeTagExtensionDefinitions();
}
