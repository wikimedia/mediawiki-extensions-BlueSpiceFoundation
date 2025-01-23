<?php

namespace BlueSpice\Data\Categories;

use MediaWiki\Context\IContextSource;
use MediaWiki\Title\Title;

class SecondaryDataProvider extends \MWStake\MediaWiki\Component\DataStore\SecondaryDataProvider {

	/**
	 *
	 * @var \MediaWiki\Linker\LinkRenderer
	 */
	protected $linkrenderer = null;

	/**
	 * @var IContextSource
	 */
	protected $context;

	/**
	 *
	 * @param \MediaWiki\Linker\LinkRenderer $linkrenderer
	 * @param IContextSource $context
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
