<?php

namespace BlueSpice;

use MediaWiki\Context\RequestContext;
use MWStake\MediaWiki\Component\AlertBanners\AlertProviderFactory\Base;

class LegacyExtensionAttributesAlertProviderFactory extends Base {

	/**
	 * Overriding the base constructor as we do not need the DI from it
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function processProviders( $providers ) {
		$skin = RequestContext::getMain()->getOutput()->getSkin();
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationAlertProviderRegistry'
		);

		foreach ( $registry->getAllKeys() as $registryKey ) {
			$callback = $registry->getValue( $registryKey );
			$this->currentAlertProvider = call_user_func_array( $callback, [ $skin ] );

			$this->checkHandlerInterface( $registryKey );

			$providers[$registryKey] = $this->currentAlertProvider;
		}

		return $providers;
	}
}
