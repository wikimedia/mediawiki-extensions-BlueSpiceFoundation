<?php
/**
 * Maintenance script to execure "action=purge" on a set of pages
 *
 * @file
 * @ingroup Maintenance
 * @license GPL-3.0-only
 */

require_once __DIR__ . '/../../../maintenance/Maintenance.php';

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class PurgePages extends Maintenance {
	public function execute() {
		$dbr = $this->getDB( DB_REPLICA );
		$res = $dbr->select(
			'page',
			'*',
			'',
			__METHOD__
		);
		$titles = [];
		$wikiPageFactory = MediaWikiServices::getInstance()->getWikiPageFactory();
		foreach ( $res as $row ) {
			$title = Title::newFromRow( $row );
			$titles[$title->getPrefixedDBkey()] = $title;
		}
		ksort( $titles );
		foreach ( $titles as $title ) {
			$wikipage = $wikiPageFactory->newFromTitle( $title );

			// Mimic "action=purge" in the webbrowser
			$success = $wikipage->doPurge();
			$status = $success ? 'DONE' : 'FAILED';

			// Mimic the "view" after calling "action=purge" on the webbrowser
			$wikipage->doViewUpdates( User::newSystemUser( 'Mediawiki default' ) );

			$this->output( "{$title->getPrefixedDBKey()} -> $status\n" );
		}
	}

}

$maintClass = PurgePages::class;
require_once RUN_MAINTENANCE_IF_MAIN;
