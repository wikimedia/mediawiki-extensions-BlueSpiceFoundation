<?php

use MediaWiki\Json\FormatJson;

require_once __DIR__ . '/BSMaintenance.php';

class SyncI18NFileKeys extends BSMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription(
			"Used to restore message keys after automatic translation of a I18N json by services like DeepL"
		);
		$this->requireExtension( 'BlueSpiceFoundation' );

		$this->addOption( 'base', 'Base I18N JSON file' );
		$this->addOption( 'variant', 'Variant I18N JSON file to have the keys synced' );
	}

	public function execute() {
		$baseFile = realpath( $this->getOption( 'base' ) );
		$variantFile = realpath( $this->getOption( 'variant' ) );

		$this->output( "Base: $baseFile" );
		$this->output( "Variant: $variantFile" );

		$baseData = FormatJson::decode( file_get_contents( $baseFile ), true );
		$variantData = FormatJson::decode( file_get_contents( $variantFile ), true );
		$newVariantData = [];
		$variantValues = array_values( $variantData );

		$index = 0;
		foreach ( $baseData as $i18nKey => $val ) {
			$newVariantData[$i18nKey] = $variantValues[$index];
			$index++;
		}

		$this->output(
			FormatJson::encode( $newVariantData, true, FormatJson::UTF8_OK | JSON_UNESCAPED_UNICODE )
		);
	}
}

$maintClass = SyncI18NFileKeys::class;
require_once RUN_MAINTENANCE_IF_MAIN;
