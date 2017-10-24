<?php

namespace BlueSpice;

interface ISetting {
	const MAIN_PATH_TYPE = 'type';
	const MAIN_PATH_EXTENSION = 'extension';
	const MAIN_PATH_PACKAGE = 'package';

	const TYPE_SYSTEM = 'system';
	const TYPE_INTERFACE = 'interface';
	const TYPE_EDIT = 'edit';
	const TYPE_SEARCH = 'search';

	/**
	 * @return string The variable name like it would be in 'LocalSettings.php'.
	 * E.g. 'wgLogo' or 'bsgPingInterval'
	 */
	public function getVariableName();

	/**
	 * @return array An array of paths that define where to provide an input
	 * field within the settings UI. E.g.
	 * [ 'type/interface', 'extension/<extensionX>', 'package/<packageX>' ]
	 * ATTENTION: Path elements need a message key to be available following
	 * the pattern 'bs-setting-path-<elementName>'. E. g.
	 * 'bs-setting-path-<extensionX>'
	 */
	public function getPaths();

	/**
	 *
	 * @return \HTMLFormField A ready to use HTML-form-field-descriptor, with
	 * default, labels, descriptions, ...
	 */
	public function getHtmlFormField();

	/**
	 *
	 * @return boolean, If this variable is stored in the database
	 */
	public function isStored();

	/**
	 *
	 * @return string, the message key for the label
	 */
	public function getLabelMessageKey();
}
