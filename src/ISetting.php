<?php

namespace BlueSpice;

interface ISetting {

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
	 * @return string, the message key for the label
	 */
	public function getLabelMessageKey();

	/**
	 *
	 * @return string, the message key for the help message | null
	 */
	public function getHelpMessageKey();

	/**
	 *
	 * @return bool, is the config currently defined as hidden
	 */
	public function isHidden();
}
