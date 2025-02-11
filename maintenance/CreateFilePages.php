<?php

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\ContentHandler;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Title\Title;

require_once dirname( dirname( dirname( __DIR__ ) ) ) . "/maintenance/Maintenance.php";

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
		$user = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
			->getMaintenanceUser()->getUser();
		foreach ( $res as $row ) {
			$this->output( $row->img_name . PHP_EOL );

			if ( $this->getOption( 'dry' ) ) {
				continue;
			}

			$title = Title::makeTitle( NS_FILE, $row->img_name );
			$wikipage = $wikiPageFactory->newFromTitle( $title );

			$text = '-';
			$summary = 'Restore file description page';
			$content = ContentHandler::makeContent( $text, $title );
			$updater = $wikipage->newPageUpdater( $user );
			$updater->setContent( SlotRecord::MAIN, $content );
			$comment = CommentStoreComment::newUnsavedComment( $summary );
			try {
				$updater->saveRevision( $comment );
			} catch ( Exception $e ) {
				$this->error( $e->getMessage() );
			}
			if ( $updater->wasSuccessful() ) {
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
