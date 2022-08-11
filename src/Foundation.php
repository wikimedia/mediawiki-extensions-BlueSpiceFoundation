<?php

namespace BlueSpice;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\LessVars;

class Foundation {

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $services = null;

	public static function onRegistry() {
		require_once dirname( __DIR__ ) . "/includes/Defines.php";
		require_once dirname( __DIR__ ) . "/includes/DefaultSettings.php";
		require_once dirname( __DIR__ ) . "/includes/GlobalFunctions.php";

		\mwsInitComponents();

		$dynamicSettingsManager = DynamicSettingsManager::factory();
		$locals = [];
		$dynamicSettingsManager->applyAll( $locals );
		foreach ( $locals as $globalKey => $globalVal ) {
			if ( is_array( $GLOBALS[$globalKey] ) ) {
				$GLOBALS[$globalKey] = array_merge( $GLOBALS[$globalKey], $locals[$globalKey] );
			} else {
				$GLOBALS[$globalKey] = $locals[$globalKey];
			}
		}

		// currently there is no other way
		\HTMLForm::$typeMappings['staticimage'] = 'HTMLStaticImageFieldOverride';
		\HTMLForm::$typeMappings['link'] = 'HTMLInfoFieldOverride';
		\HTMLForm::$typeMappings['text'] = 'HTMLTextFieldOverride';
		\HTMLForm::$typeMappings['int'] = 'HTMLIntFieldOverride';
		\HTMLForm::$typeMappings['multiselectex'] = 'HTMLMultiSelectEx';
		\HTMLForm::$typeMappings['multiselectplusadd'] = 'HTMLMultiSelectPlusAdd';
		\HTMLForm::$typeMappings['multiselectsort'] = 'HTMLMultiSelectSortList';

		if ( !isset( $GLOBALS['wgExtensionFunctions'] ) ) {
			$GLOBALS['wgExtensionFunctions'] = [];
		}
		// initalise BlueSpice as first extension in a fully initialised
		// environment
		$foundation = new self;
		array_unshift(
			$GLOBALS['wgExtensionFunctions'],
			static function () use( $foundation ) {
				$foundation->initialize();
			}
		);
	}

	/**
	 * Set less variables
	 */
	protected function setLessVars() {
		$lessVars = LessVars::getInstance();

		// Set defaults
		$lessVars->setVar( 'primary-bg', '#3e5389' );
		$lessVars->setVar( 'primary-fg', '#ffffff' );
		$lessVars->setVar( 'secondary-bg', '#ffffff' );
		$lessVars->setVar( 'secondary-fg', '#666666' );
		$lessVars->setVar( 'neutral-bg', '#929292' );
		$lessVars->setVar( 'neutral-fg', '#666666' );
		$lessVars->setVar( 'bs-color-primary', '@primary-bg' );
		$lessVars->setVar( 'bs-color-secondary', '#ffae00' );
		$lessVars->setVar( 'bs-color-tertiary', '#b73a3a' );
		$lessVars->setVar( 'bs-color-neutral', '@neutral-bg' );
		$lessVars->setVar( 'bs-color-neutral2', '#ABABAB' );
		$lessVars->setVar( 'bs-color-neutral3', '#C4C4C4' );
		$lessVars->setVar( 'bs-color-neutral4', '#787878' );
		$lessVars->setVar( 'bs-color-secondary-information', 'darken( @neutral-bg, 17.3% )' );
		$lessVars->setVar( 'bs-color-progressive', '#347bff' );
		$lessVars->setVar( 'bs-color-constructive', '#00af89' );
		$lessVars->setVar( 'bs-color-destructive', '#d11d13' );
		$lessVars->setVar( 'bs-color-success', '#dff0d8' );
		$lessVars->setVar( 'bs-color-warning', '#fcf8e3' );
		$lessVars->setVar( 'bs-color-error', '#f2dede' );
		$lessVars->setVar( 'bs-color-info', '#d9edf7' );
		$lessVars->setVar( 'bs-background-neutral', '#FFFFFF' );
		$lessVars->setVar( 'bs-background-primary', 'none' );
		$lessVars->setVar( 'bs-loading-indicator-color', '#ffae00' );
		$lessVars->setVar( 'bs-color-link', '#0060DF' );
		$lessVars->setVar( 'bs-color-link-hover', '#006EFF' );
		$lessVars->setVar( 'bs-color-link-new', '#B73A3A' );
		$lessVars->setVar( 'bs-color-link-new-hover', '#E92121' );

		// Override with values from registry - legacy
		$registry = new \BlueSpice\ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationLessVarsRegistry'
		);

		foreach ( $registry->getAllKeys() as $key ) {
			$lessVars->setVar( $key, $registry->getValue( $key ) );
		}

		// Override with values from config - legacy
		foreach ( $this->config->get( 'LessVars' ) as $key => $value ) {
			$lessVars->setVar( $key, $value );
		}
	}

	private function __construct() {
	}

	protected function initialize() {
		// earliest possible position to use the services and config
		$this->services = MediaWikiServices::getInstance();
		$this->config = $this->services->getConfigFactory()->makeConfig( 'bsg' );

		$this->initializeNotifications();
		$this->initializeExtensions();
		$this->overwriteGlobals();
		$this->initializeRoleSystem();
		$this->setLessVars();
	}

	protected function initializeNotifications() {
		$notifications = $this->services->getService( 'BSNotificationManager' );
		$notifications->init();
	}

	protected function initializeExtensions() {
		$this->services->getService( 'BSExtensionFactory' )->getExtensions();
	}

	protected function overwriteGlobals() {
		$cfgDfn = $this->services->getService( 'BSConfigDefinitionFactory' );
		foreach ( $cfgDfn->getRegisteredDefinitions() as $name ) {
			$instance = $cfgDfn->factory( $name );
			if ( !$instance instanceof IOverwriteGlobal ) {
				continue;
			}
			$GLOBALS[$instance->getGlobalName()] = $instance->getValue();
		}
		$GLOBALS['wgFileExtensions'] = array_values( array_unique( array_merge(
			$GLOBALS['wgFileExtensions'],
			$this->config->get( 'ImageExtensions' )
		) ) );

		// Adaptions of `mwstake/mediawiki-component-runjobstrigger`
		$GLOBALS['mwsgRunJobsTriggerHandlerFactories']['legacy-bluespice-extension-attributes'] = [
			'class' => "\\BlueSpice\\RunJobsTriggerHandler\\LegacyExtensionAttributesFactory"
		];
		$GLOBALS['mwsgRunJobsTriggerOptions'] = wfArrayPlus2d(
			$this->config->get( 'RunJobsTriggerHandlerOptions' ),
			$GLOBALS['mwsgRunJobsTriggerOptions']
		);
		$GLOBALS['mwsgRunJobsTriggerRunnerWorkingDir'] = BSDATADIR;

		// Adaptions of `mwstake/mediawiki-component-alertbanners`
		$GLOBALS['mwsgAlertBannersProviderFactories']['legacy-bluespice-extension-attributes'] = [
			'class' => "\\BlueSpice\\LegacyExtensionAttributesAlertProviderFactory"
		];
		// Adaptions of `mwstake/mediawiki-component-manifestregistry`
		$GLOBALS['mwsgManifestRegistryOverrides'] = array_merge(
			$this->config->get( 'ExtensionAttributeRegistryOverrides' ),
			$GLOBALS['mwsgManifestRegistryOverrides']
		);
	}

	protected function initializeRoleSystem() {
		if ( !$this->config->get( 'EnableRoleSystem' ) ) {
			return;
		}
		$roleManager = $this->services->getService( 'BSRoleManager' );
		$roleManager->enableRoleSystem();
	}

}
