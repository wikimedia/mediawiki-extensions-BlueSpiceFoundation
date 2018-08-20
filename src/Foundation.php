<?php


namespace BlueSpice;


class Foundation {

	public static function onRegistry(){

		require_once dirname( __DIR__ ) . "/includes/Defines.php";
		require_once dirname( __DIR__ ) . "/includes/DefaultSettings.php";

		if( !isset( $GLOBALS['wgExtensionFunctions'] ) ) {
			$GLOBALS['wgExtensionFunctions'] = [];
		}
		// initalise BlueSpice as first extension in a fully initialised
		// environment
		array_unshift(
			$GLOBALS['wgExtensionFunctions'],
			'BsCore::doInitialise'
		);

		//currently there is no other way
		\HTMLForm::$typeMappings['staticimage'] = 'HTMLStaticImageFieldOverride';
		\HTMLForm::$typeMappings['link'] = 'HTMLInfoFieldOverride';
		\HTMLForm::$typeMappings['text'] = 'HTMLTextFieldOverride';
		\HTMLForm::$typeMappings['int'] = 'HTMLIntFieldOverride';
		\HTMLForm::$typeMappings['multiselectex'] = 'HTMLMultiSelectEx';
		\HTMLForm::$typeMappings['multiselectplusadd'] = 'HTMLMultiSelectPlusAdd';
		\HTMLForm::$typeMappings['multiselectsort'] = 'HTMLMultiSelectSortList';
	}
}
