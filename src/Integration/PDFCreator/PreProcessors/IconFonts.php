<?php

namespace BlueSpice\Integration\PDFCreator\PreProcessors;

use MediaWiki\Extension\PDFCreator\IPreProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;

class IconFonts implements IPreProcessor {

	/**
	 * @param ExportPage[] &$pages
	 * @param array &$images
	 * @param array &$attachments
	 * @param ExportContext $context
	 * @param string $module
	 * @param array $params
	 * @return void
	 */
	public function execute( array &$pages, array &$images, array &$attachments,
		ExportContext $context, string $module = '', $params = []
	): void {
		$dir = dirname( __DIR__, 4 );

		$images['entypo.ttf'] = "$dir/resources/entypo/entypo.ttf";
		$images['fontawesome.ttf'] = "$dir/resources/fontawesome/fontawesome.ttf";
		$images['icomoon.ttf'] = "$dir/resources/icomoon/icomoon.ttf";
	}
}
