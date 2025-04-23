<?php

use BlueSpice\InstanceStatus\IStatusProvider;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\ManifestRegistry\ManifestAttributeBasedRegistry;
use MWStake\MediaWiki\Component\ManifestRegistry\ManifestRegistryFactory;

require_once __DIR__ . '/BSMaintenance.php';

/**
 * Maintenance script to check the status of all registered
 * BlueSpiceFoundation StatusCheckProviders.
 */
class StatusCheck extends BSMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Checks the status of all BlueSpiceFoundation StatusCheckProviders.' );
	}

	public function execute() {
		/** @var ManifestRegistryFactory $factory */
		$factory = MediaWikiServices::getInstance()->getService( 'MWStakeManifestRegistryFactory' );
		/** @var ManifestAttributeBasedRegistry $registry */
		$registry = $factory->get( 'BlueSpiceFoundationStatusCheckProvider' );

		$objectFactory = MediaWikiServices::getInstance()->getObjectFactory();
		$keys = $registry->getAllKeys();
		$results = [];

		foreach ( $keys as $key ) {
			$spec = $registry->getObjectSpec( $key );
			try {
				$instance = $objectFactory->createObject( $spec );

				if ( !( $instance instanceof IStatusProvider ) ) {
					$results[ $key ] = "Does not implement IStatusProvider";
					continue;
				}

				$results[ $instance->getLabel() ] = $instance->getValue();
			} catch ( Exception $e ) {
				$results[ $key ] = 'Exception: ' . $e->getMessage();
			}
		}

		$jsonString = FormatJson::encode( $results, true );
		$this->output( $jsonString );
	}
}

$maintClass = StatusCheck::class;
require_once RUN_MAINTENANCE_IF_MAIN;
