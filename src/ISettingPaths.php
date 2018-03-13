<?php

namespace BlueSpice;

interface ISettingPaths {
	const MAIN_PATH_FEATURE = 'feature';
	const MAIN_PATH_EXTENSION = 'extension';
	const MAIN_PATH_PACKAGE = 'package';

	const FEATURE_SYSTEM = 'system';
	const FEATURE_DATA_ANALYSIS = 'dataanalysis';
	const FEATURE_EDITOR = 'editor';
	const FEATURE_SEARCH = 'search';
	const FEATURE_PERSONALISATION = 'personalisation';
	const FEATURE_SKINNING = 'skinning';
	const FEATURE_CONTENT_STRUCTURING = 'contentstructuring';
	const FEATURE_COMMUNICATION = 'communication';
	const FEATURE_ADMINISTRATION = 'administration';
	const FEATURE_QUALITY_ASSURANCE = 'qualityassurance';
	const FEATURE_EXPORT = 'export';

	const PACKAGE_FREE = 'BlueSpice Free';
	const PACKAGE_PRO = 'BlueSpice Pro';
	const PACKAGE_CUSTOMIZING = 'customizing';

	const EXTENSION_FOUNDATION = 'BlueSpiceFoundation';
}
