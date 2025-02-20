<?php

namespace BlueSpice\Integration\PDFCreator\StylesheetsProvider;

use MediaWiki\Extension\PDFCreator\IStylesheetsProvider;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;

class IconFontsStyles implements IStylesheetsProvider {

	/**
	 * @param string $module
	 * @param ExportContext $context
	 * @return array
	 */
	public function execute( string $module, ExportContext $context ): array {
		$dir = dirname( __DIR__, 4 );

		return [
			'entypo.ttf' => "$dir/resources/entypo/entypo.ttf",
			'fontawesome.ttf' => "$dir/resources/fontawesome/fontawesome.ttf",
			'icomoon.ttf' => "$dir/resources/icomoon/icomoon.ttf",
			'entypo.css' => "$dir/resources/entypo/entypo.css",
			'entypo-fonts.css' => "$dir/resources/entypo/entypo-pdf-integration.css",
			'fontawesome.css' => "$dir/resources/fontawesome/fontawesome.css",
			'fontawesome-fonts.css' => "$dir/resources/fontawesome/fontawesome-pdf-integration.css",
			'icomoon.css' => "$dir/resources/icomoon/icomoon.css",
			'icomoon-fonts.css' => "$dir/resources/icomoon/icomoon-pdf-integration.css"
		];
	}
}
