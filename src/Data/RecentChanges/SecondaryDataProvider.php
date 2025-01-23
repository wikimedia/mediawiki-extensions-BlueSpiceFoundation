<?php

namespace BlueSpice\Data\RecentChanges;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
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

		$title = Title::newFromText( $rawData->page_prefixedtext );
		if ( $title instanceof Title === false ) {
			return;
		}
		$rawData->page_link = $this->linkrenderer->makeLink( $title );

		$user = MediaWikiServices::getInstance()->getUserFactory()
			->newFromID( $rawData->tmp_user );
		if ( $user->isRegistered() ) {
			$rawData->user_link =
				$this->linkrenderer->makeLink( $user->getUserPage() );
		}

		// whenever this is the first revision of a page do not generate a link to
		// the diff, as it would be broken
		$rawData->diff_url = '';
		$rawData->diff_link = '';
		if ( !empty( $rawData->last_oldid ) ) {
			$diffQuery = [
				'type' => 'revision',
				'curid' => $rawData->cur_id,
				'oldid' => $rawData->last_oldid,
				'diff' => $rawData->this_oldid
			];
			$rawData->diff_url = $title->getFullURL( $diffQuery );
			$rawData->diff_link =
				$this->linkrenderer->makeLink(
				$title,
				wfMessage( 'diff' ),
				[],
				$diffQuery
			);
		}
		$histQuery = [
			'curid' => $rawData->cur_id,
			'action' => 'history'
		];
		$rawData->hist_url = $title->getFullURL( $histQuery );
		$rawData->hist_link =
			$this->linkrenderer->makeLink(
				$title,
				wfMessage( 'pagehist' ),
				[],
				$histQuery
			);

		$oldIdQuery = [ 'oldid' => $rawData->last_oldid ];
		$rawData->{Record::OLDID_URL} = $title->getFullURL( $oldIdQuery );
		$rawData->{Record::OLDID_LINK} = $this->linkrenderer->makeLink(
			$title,
			null,
			[],
			$oldIdQuery
		);

		$rawData->timestamp = $this->context->getLanguage()->userTimeAndDate(
			$rawData->raw_timestamp,
			$this->context->getUser()
		);

		unset( $rawData->tmp_user );

		$dataSet = new Record( $rawData );
	}
}
