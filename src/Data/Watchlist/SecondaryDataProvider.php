<?php

namespace BlueSpice\Data\Watchlist;

use ContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class SecondaryDataProvider extends \MWStake\MediaWiki\Component\DataStore\SecondaryDataProvider {

	/**
	 *
	 * @var \MediaWiki\Linker\LinkRenderer
	 */
	protected $linkrenderer = null;

	/**
	 *
	 * @var ContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param \MediaWiki\Linker\LinkRenderer $linkrenderer
	 * @param ContextSource $context
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
		$services = MediaWikiServices::getInstance();
		$user = $services->getUserFactory()->newFromId( $dataSet->get( Record::USER_ID ) );
		$dataSet->set(
			Record::USER_LINK,
			$this->linkrenderer->makeLink( $user->getUserPage() )
		);

		$title = Title::newFromText( $dataSet->get( Record::PAGE_PREFIXED_TEXT ) );
		$dataSet->set(
			Record::PAGE_LINK,
			$this->linkrenderer->makeLink( $title )
		);

		if ( $dataSet->get( Record::HAS_UNREAD_CHANGES ) ) {
			$lookup = $services->getRevisionLookup();
			$rev = $lookup->getRevisionByTimestamp(
				$title,
				$dataSet->get( Record::NOTIFICATIONTIMESTAMP )
			);
			if ( $rev ) {
				$dataSet->set( Record::UNREAD_CHANGES_DIFF_REVID, $rev->getId() );
			}
		}
	}

}
