<?php

namespace BlueSpice\Data\Watchlist;

class SecondaryDataProvider extends \BlueSpice\Data\SecondaryDataProvider {

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

	protected function doExtend( &$dataSet ){
		$user = \User::newFromId( $dataSet->get( Record::USER_ID ) );
		$dataSet->set(
			Record::USER_LINK,
			$this->linkrenderer->makeLink( $user->getUserPage() )
		);

		$title = \Title::newFromText( $dataSet->get( Record::PAGE_PREFIXED_TEXT ) );
		$dataSet->set(
			Record::PAGE_LINK,
			$this->linkrenderer->makeLink( $title )
		);
	}
}
