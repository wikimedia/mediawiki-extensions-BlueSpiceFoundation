<?php

namespace BlueSpice;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use MediaWiki\Config\Config;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\MediaWikiServices;

class Foundation {

	/**
	 *
	 * @var Config
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

		// currently there is no other way
		HTMLForm::$typeMappings['staticimage'] = 'HTMLStaticImageFieldOverride';
		HTMLForm::$typeMappings['link'] = 'HTMLInfoFieldOverride';
		HTMLForm::$typeMappings['text'] = 'HTMLTextFieldOverride';
		HTMLForm::$typeMappings['int'] = 'HTMLIntFieldOverride';
		HTMLForm::$typeMappings['multiselectex'] = 'HTMLMultiSelectEx';
		HTMLForm::$typeMappings['multiselectplusadd'] = 'HTMLMultiSelectPlusAdd';
		HTMLForm::$typeMappings['multiselectsort'] = 'HTMLMultiSelectSortList';

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

	private function __construct() {
	}

	protected function initialize() {
		// earliest possible position to use the services and config
		$this->services = MediaWikiServices::getInstance();
		$this->config = $this->services->getConfigFactory()->makeConfig( 'bsg' );

		$this->initializeExtensions();
		$this->overwriteGlobals();
		$this->initializeRoleSystem();
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

			// Since there is a global array wgLogos with different sizes
			// and ConfigDefinitior does not handle arrays we have to do this ugly fix
			if ( $instance->getGlobalName() === 'wgLogo' ) {
				$GLOBALS['wgLogos']['1x'] = $instance->getValue();
			}
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
