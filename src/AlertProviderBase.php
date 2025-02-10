<?php

namespace BlueSpice;

use MediaWiki\Config\MultiConfig;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\AlertBanners\AlertProviderBase as AlertBannersAlertProviderBase;

abstract class AlertProviderBase extends AlertBannersAlertProviderBase {

	/**
	 * @inheritDoc
	 */
	public function __construct( $skin, $loadBalancer, $config ) {
		parent::__construct( $skin, $loadBalancer, $config );

		$bsgConfig = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		$this->config = new MultiConfig( [
			$bsgConfig,
			$this->config
		] );
	}

}
