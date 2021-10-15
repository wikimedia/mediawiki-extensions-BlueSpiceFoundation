<?php

namespace BlueSpice;

interface ISettingPaths {
	public const MAIN_PATH_FEATURE = 'feature';
	public const MAIN_PATH_EXTENSION = 'extension';
	public const MAIN_PATH_PACKAGE = 'package';

	public const FEATURE_SYSTEM = 'system';
	public const FEATURE_DATA_ANALYSIS = 'dataanalysis';
	public const FEATURE_EDITOR = 'editor';
	public const FEATURE_SEARCH = 'search';
	public const FEATURE_PERSONALISATION = 'personalisation';
	public const FEATURE_SKINNING = 'skinning';
	public const FEATURE_CONTENT_STRUCTURING = 'contentstructuring';
	public const FEATURE_COMMUNICATION = 'communication';
	public const FEATURE_ADMINISTRATION = 'administration';
	public const FEATURE_QUALITY_ASSURANCE = 'qualityassurance';
	public const FEATURE_EXPORT = 'export';

	public const PACKAGE_FREE = 'BlueSpice Free';
	public const PACKAGE_PRO = 'BlueSpice Pro';
	public const PACKAGE_CLOUD = 'BlueSpice Cloud';
	public const PACKAGE_CUSTOMIZING = 'customizing';

	public const EXTENSION_FOUNDATION = 'BlueSpiceFoundation';
}
