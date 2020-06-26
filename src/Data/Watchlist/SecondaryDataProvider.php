<?php

namespace BlueSpice\Data\Watchlist;

use ApiMain;
use ContextSource;
use DerivativeRequest;

class SecondaryDataProvider extends \BlueSpice\Data\SecondaryDataProvider {

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

		if ( $dataSet->get( Record::HAS_UNREAD_CHANGES ) ) {
			$this->getUnreadChangesDiffRevidFromNotificationTimestamp( $dataSet );
		}
	}

	/**
	 *
	 * @param Record &$dataSet
	 */
	private function getUnreadChangesDiffRevidFromNotificationTimestamp( &$dataSet ) {
		$pageId = $dataSet->get( Record::PAGE_ID );
		$notificationTimestamp = $dataSet->get( Record::NOTIFICATIONTIMESTAMP );

		// This is not ideal. Unfortunately the MediaWiki object model does not have any functions
		// for "get closest revision for timestamp". The Action API has such functionality, but
		// makes direct database access. This is to avoid duplicate code.
		$params = new DerivativeRequest( $this->context->getRequest(), [
			'action' => 'query',
			'prop' => 'revisions',
			'pageids' => $pageId,
			'rvstart' => $notificationTimestamp,
			'rvlimit' => 1,
			'rvprop' => 'ids'
		] );
		$api = new ApiMain( $params );
		$api->execute();
		$data = $api->getResult()->getResultData();

		$revId = -1;
		if ( isset( $data['query']['pages'][$pageId]['revisions'][0]['revid'] ) ) {
			$revId = $data['query']['pages'][$pageId]['revisions'][0]['revid'];
		}

		$dataSet->set( Record::UNREAD_CHANGES_DIFF_REVID, $revId );
	}
}
