<?php

namespace BlueSpice\Hook\SiteNoticeAfter;

use BlueSpice\Hook\SiteNoticeAfter;
use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\IAlertProvider;
use Html;
use Exception;

class AddAlerts extends SiteNoticeAfter {

	protected $container = '';

	protected $alerts = [];

	protected function doProcess() {
		$this->collectAlerts();
		$this->buildContainer();
		$this->addContainer();

		return true;
	}

	protected function collectAlerts() {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationAlertProviderRegistry'
		);

		foreach( $registry->getAllKeys() as $registryKey ) {
			$callback = $registry->getValue( $registryKey );
			$provider = call_user_func_array( $callback, [ $this->skin ] );

			if( $provider instanceof IAlertProvider === false ) {
				throw new Exception(
					"Factory callback of '$registryKey' does not "
						. "implement IAlertProvider!"
				);
			}
			$this->alerts[$registryKey] =
				$this->makeAlertHTML( $registryKey, $provider );
		}
	}

	protected function buildContainer() {
		$rawContentHtml = '';

		foreach( $this->alerts as $regKey => $html ) {
			$rawContentHtml .= $html;
		}

		$this->container = Html::rawElement(
			'div' ,
			[
				'id' => 'bs-alert-container'
			],
			$rawContentHtml
		);
	}

	/**
	 * HINT: This is basically what Extension:CentralNotice does, therefore I
	 * consider it to be a good approach
	 * https://github.com/wikimedia/mediawiki-extensions-CentralNotice/blob/949a5edc36a7ecb86642fc608633dd7336be36a4/CentralNotice.hooks.php#L277-L281
	 */
	protected function addContainer() {
		$this->siteNotice .= $this->container;
	}

	/**
	 *
	 * @param string $registryKey
	 * @param IAlertProvider $provider
	 * @return string
	 */
	protected function makeAlertHTML( $registryKey, $provider ) {
		$type = $provider->getType();
		$html = $provider->getHTML();

		if( empty( $html ) ) {
			return '';
		}

		return Html::rawElement(
			'div',
			[
				'class' => 'alert alert-' . $type,
				'role' =>"alert",
				'data-bs-alert-id' => $registryKey
			],
			$html
		);
	}

}
