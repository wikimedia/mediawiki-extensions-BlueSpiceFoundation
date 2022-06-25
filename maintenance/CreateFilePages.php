<?php

require_once dirname( dirname( dirname( __DIR__ ) ) ) . "/maintenance/Maintenance.php";

use MediaWiki\MediaWikiServices;

class CreateFilePages extends Maintenance {

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption( 'dry', 'Just outputs problematic pages without actual changes in DB' );
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$dbr = $this->getDB( DB_REPLICA );

		$res = $dbr->select(
			[
				'image',
				'page'
			],
			'img_name',
			[
				'page_id IS NULL'
			],
			__METHOD__,
			[],
			[
				'page' => [
					'LEFT JOIN',
					[ 'img_name = page_title' ]
				]
			]
		);

		if ( $res->numRows() > 0 ) {
			$this->output( 'Some files do not have corresponding wiki page yet:' . PHP_EOL );
		} else {
			$this->output( 'All files have corresponding pages.' . PHP_EOL );
			return;
		}

		$wikiPageFactory = MediaWikiServices::getInstance()->getWikiPageFactory();
		foreach ( $res as $row ) {
			$this->output( $row->img_name . PHP_EOL );

			if ( $this->getOption( 'dry' ) ) {
				continue;
			}

			$title = Title::makeTitle( NS_FILE, $row->img_name );
			$wikipage = $wikiPageFactory->newFromTitle( $title );

			$text = '-';
			$comment = 'Restore file description page';

			$content = ContentHandler::makeContent( $text, $title );
			$status = $wikipage->doEditContent( $content, $comment );

			if ( $status->isGood() ) {
				$this->output( 'Page created successfully...' . PHP_EOL );
			} else {
				$this->output( 'Page was not created due to some error!' . PHP_EOL );
			}
		}

		$this->output( 'Done!' . PHP_EOL );
	}
}

$maintClass = CreateFilePages::class;
require_once RUN_MAINTENANCE_IF_MAIN;
