<?php

namespace BlueSpice;

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use BlueSpiceFoundationTagRegistry
 * attribute in extension.json
 */
interface ITagExtensionDefinitionProvider {

	/**
	 * DEPRECATED!
	 * Returns an array of tag extension definitions
	 * @deprecated since version 3.1 - Use BlueSpiceFoundationTagRegistry
	 * attribute in extension.json
	 * @return array
	 */
	public function makeTagExtensionDefinitions();
}
