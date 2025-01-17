<?php

namespace BlueSpice\Data\Templatelinks;

use MediaWiki\Title\Title;

class SecondaryDataProvider extends \MWStake\MediaWiki\Component\DataStore\SecondaryDataProvider {

	/**
	 *
	 * @var \MediaWiki\Linker\LinkRenderer
	 */
	protected $linkrenderer = null;

	/**
	 *
	 * @param \MediaWiki\Linker\LinkRenderer $linkrenderer
	 */
	public function __construct( $linkrenderer ) {
		$this->linkrenderer = $linkrenderer;
	}

	/**
	 *
	 * @param Record &$dataSet
	 */
	protected function doExtend( &$dataSet ) {
		$title = Title::newFromText(
			$dataSet->get( Record::TEMPLATE_TITLE ),
			$dataSet->get( Record::TEMPLATE_NS_ID )
		);

		$dataSet->set(
			Record::TEMPLATE_LINK,
			$this->linkrenderer->makeLink( $title, $title->getText() )
		);
	}
}
