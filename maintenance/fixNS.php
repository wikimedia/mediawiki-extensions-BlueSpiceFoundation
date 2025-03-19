<?php

use MediaWiki\Maintenance\Maintenance;

require_once __DIR__ . '/Maintenance.php';

/**
 * Base class for adding pages with retrospectively to a namespace
 *
 * @ingroup Maintenance
 */
class FixNS extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( '' );
		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	public function execute() {
		$dbw = $this->getDB( DB_PRIMARY );
		$res = $dbw->select(
			"page",
			[ "page_id", "page_title", "page_namespace" ],
			[
				"page_namespace = 0",
				"page_namespace " . $dbw->buildLike( "%:%", $dbw->anyString() )
			],
			__METHOD__
		);

		if ( $res != null ) {
			foreach ( $res as $row ) {
				$fields = get_object_vars( $row );
				$pageId = $fields[ "page_id" ];
				$pageTitle = $fields[ "page_title" ];
				$arr = explode( ":", $pageTitle, 2 );
				$pageNS = $arr[ 0 ];
				$pageName = $arr[ 1 ];
				$arrNS = $GLOBALS[ 'wgExtraNamespaces' ];

				if ( in_array( $pageNS, $arrNS ) ) {
					$this->output( "Found " . $pageNS . " as namespace. \n" );
					$this->output( "Adding " . $pageTitle . " to the namespace " . $pageNS . ". \n" );
					foreach ( $arrNS as $nsKey => $nsData ) {
						if ( $nsData == $pageNS ) {
							$dbw->update(
								"page",
								[
									"page_title = '" . $pageName . "'",
									"page_namespace = " . $nsKey
								],
								[
									"page_title = '" . $nsData . ":" . $pageName . "'",
									"page_id = " . $pageId,
									"page_namespace = 0"
								],
								__METHOD__
							);
							$done = true;
						}
					}

					if ( $done == true ) {
						$this->output( " ... done\n" );
					} else {
						$this->output( " ... query failed \n" );
					}
				} else {
					$this->output( "No page in need of change found. \n" );
				}
			}
		} else {
			$this->output( " .. query failed \n " );
		}
	}
}

$maintClass = FixNS::class;
require_once RUN_MAINTENANCE_IF_MAIN;
