<?php


namespace BlueSpice;

use BlueSpice\Services;

class Foundation {

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var Services
	 */
	protected $services = null;

	public static function onRegistry(){

		require_once dirname( __DIR__ ) . "/includes/Defines.php";
		require_once dirname( __DIR__ ) . "/includes/DefaultSettings.php";

		//currently there is no other way
		\HTMLForm::$typeMappings['staticimage'] = 'HTMLStaticImageFieldOverride';
		\HTMLForm::$typeMappings['link'] = 'HTMLInfoFieldOverride';
		\HTMLForm::$typeMappings['text'] = 'HTMLTextFieldOverride';
		\HTMLForm::$typeMappings['int'] = 'HTMLIntFieldOverride';
		\HTMLForm::$typeMappings['multiselectex'] = 'HTMLMultiSelectEx';
		\HTMLForm::$typeMappings['multiselectplusadd'] = 'HTMLMultiSelectPlusAdd';
		\HTMLForm::$typeMappings['multiselectsort'] = 'HTMLMultiSelectSortList';

		if( !isset( $GLOBALS['wgExtensionFunctions'] ) ) {
			$GLOBALS['wgExtensionFunctions'] = [];
		}
		// initalise BlueSpice as first extension in a fully initialised
		// environment
		$foundation = new self;
		array_unshift(
			$GLOBALS['wgExtensionFunctions'],
			function() use( $foundation ) {
				$foundation->initialize();
			}
		);
	}

	private function __construct() {}

	protected function initialize() {
		//earliest possible position to use the services and config
		$this->services = Services::getInstance();
		$this->config = $this->services->getConfigFactory()->makeConfig( 'bsg' );

		$this->initializeLegacyConfig();
		$this->initializeNotifications();
		$this->initializeExtensions();
		$this->overwriteGlobals();
		$this->initializeRoleSystem();
	}

	protected function initializeLegacyConfig() {
		if ( !defined( 'DO_MAINTENANCE' ) ) {
			\BsConfig::loadSettings();
		}
	}

	protected function initializeNotifications() {
		$notifications = $this->services->getBSNotificationManager();
		$notifications->init();
	}

	protected function initializeExtensions() {
		$this->services->getBSExtensionFactory()->getExtensions();
	}

	protected function overwriteGlobals() {
		global $wgFileExtensions;
		$wgFileExtensions = array_merge(
			$this->config->get( 'FileExtensions' ),
			$this->config->get( 'ImageExtensions' )
		);
		$wgFileExtensions = array_values( array_unique( $wgFileExtensions ) );
	}

	protected function initializeRoleSystem() {
		if( !$this->config->get( 'EnableRoleSystem' ) ) {
			return;
		}
		$roleManager = $this->services->getService( 'BSRoleManager' );
		$roleManager->applyRoles();
	}

}
