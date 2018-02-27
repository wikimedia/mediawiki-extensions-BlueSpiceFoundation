<?php

namespace BlueSpice;

interface ISetting {
	const MAIN_PATH_FEATURE = 'feature';
	const MAIN_PATH_EXTENSION = 'extension';
	const MAIN_PATH_PACKAGE = 'package';

	const FEATURE_SYSTEM = 'feature-system';
	const FEATURE_DATA_ANALYSIS = 'feature-dataanalysis';
	const FEATURE_EDIT = 'feature-edit';
	const FEATURE_SEARCH = 'feature-search';
	const FEATURE_PERSONALISATION = 'feature-personalisation';
	const FEATURE_SKINNING = 'feature-skinning';

	const PACKAGE_FREE = 'package-free';
	const PACKAGE_PRO = 'package-pro';
	const PACKAGE_CUSTOMIZING = 'package-customizing';

	const EXTENSION_FOUNDATION = 'BlueSpiceFoundation';

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
}
