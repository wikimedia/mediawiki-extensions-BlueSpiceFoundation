<?php

namespace BlueSpice\Data\Categories;

use BlueSpice\Data\SecondaryDataProvider as SecondaryDataProviderBase;
use Title;

class SecondaryDataProvider extends SecondaryDataProviderBase {

	/**
	 *
	 * @var \MediaWiki\Linker\LinkRenderer
	 */
	protected $linkrenderer = null;

	/**
	 * @var \IContextSource
	 */
	protected $context;

	/**
	 *
	 * @param \MediaWiki\Linker\LinkRenderer $linkrenderer
	 * @param \IContextSource $context
	 */
	public function __construct( $linkrenderer, $context ) {
		$this->linkrenderer = $linkrenderer;
		$this->context = $context;
	}

	/**
	 *
	 * @param Record &$dataSet
	 */
	protected function doExtend( &$dataSet ) {
		$rawData = $dataSet->getData();

		$title = Title::makeTitleSafe( NS_CATEGORY, $rawData->{Record::CAT_TITLE} );
		$rawData->{Record::CAT_LINK} = $this->linkrenderer->makeLink(
			$title
		);
		$dataSet = new Record( $rawData );
	}
}
