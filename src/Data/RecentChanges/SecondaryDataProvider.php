<?php

namespace BlueSpice\Data\RecentChanges;

class SecondaryDataProvider extends \BlueSpice\Data\SecondaryDataProvider {

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
	 * @param Record $dataSet
	 */
	protected function doExtend( &$dataSet ){
		$rawData = $dataSet->getData();

		$title = \Title::newFromText( $rawData->page_prefixedtext );
		$rawData->page_link = $this->linkrenderer->makeLink( $title );

		$user = \User::newFromID( $rawData->tmp_user );
		if( !$user->isAnon() ) {
			$rawData->user_link =
				$this->linkrenderer->makeLink( $user->getUserPage() );
		}

		$diffQuery = [
			'curid' => $rawData->tmp_curid,
			'oldid' => $rawData->tmp_oldid,
			'diff' => $rawData->tmp_diff
		];
		$rawData->diff_url = $title->getFullURL( $diffQuery );
		$rawData->diff_link =
			$this->linkrenderer->makeLink(
				$title,
				wfMessage( 'difference-title', $title->getPrefixedText() ),
				[],
				$diffQuery
			);

		$histQuery = [
			'curid' => $rawData->tmp_curid,
			'action' => 'history'
		];
		$rawData->hist_url = $title->getFullURL( $histQuery );
		$rawData->hist_link =
			$this->linkrenderer->makeLink(
				$title,
				wfMessage( 'history-title', $title->getPrefixedText() ),
				[],
				$histQuery
			);

		$rawData->timestamp = $this->context->getLanguage()->userTimeAndDate(
			$rawData->raw_timestamp,
			$this->context->getUser()
		);

		unset( $rawData->tmp_user );
		unset( $rawData->tmp_curid );
		unset( $rawData->tmp_oldid );
		unset( $rawData->tmp_diff );

		$dataSet = new Record( $rawData );
	}
}
