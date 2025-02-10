<?php

use MediaWiki\Json\FormatJson;
use MediaWiki\Registration\ExtensionRegistry;

require_once 'BSMaintenance.php';

class InspectManifestAttributes extends BSMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption(
			'attributeName',
			'Name of the attribute to check',
			true,
			true
		);
	}

	public function execute() {
		$attributeName = $this->getOption( 'attributeName' );
		$data = ExtensionRegistry::getInstance()->getAttribute( $attributeName );
		$jsonString = FormatJson::encode( $data, true );
		$this->output( $jsonString );
	}
}

$maintClass = InspectManifestAttributes::class;
require_once RUN_MAINTENANCE_IF_MAIN;
