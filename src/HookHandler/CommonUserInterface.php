<?php

namespace BlueSpice\HookHandler;

use BlueSpice\ExtensionAttributeBasedRegistry;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUILessVarsInit;

class CommonUserInterface implements MWStakeCommonUILessVarsInit {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUILessVarsInit( $lessVars ): void {
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
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationLessVarsRegistry'
		);

		foreach ( $registry->getAllKeys() as $key ) {
			$lessVars->setVar( $key, $registry->getValue( $key ) );
		}

		// For some reason this service can not be injected ate the time it is requried
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		// Override with values from config - legacy
		foreach ( $config->get( 'LessVars' ) as $key => $value ) {
			$lessVars->setVar( $key, $value );
		}
	}
}
