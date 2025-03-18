<?php

require_once __DIR__ . '/../../BlueSpiceFoundation/maintenance/BSMaintenance.php';

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class BSExportFiles extends BSMaintenance {

	protected $sBasePath = '';

	public function __construct() {
		parent::__construct();
		$this->addOption( 'dest', 'Path to copy files to', true, true );
		$this->addOption( 'pagelist', 'List of pages from which linked media files should be exported',
				true, true );
	}

	public function execute() {
		$sPageList = file_get_contents( $this->getOption( 'pagelist' ) );
		$sDest = str_replace( '\\', '/', $this->getOption( 'dest' ) );
		wfMkdirParents( $sDest );

		$aPageTitles = explode( "\n", $sPageList );

		$aPageIds = [];
		foreach ( $aPageTitles as $sPageTitle ) {
			$sPageTitle = trim( $sPageTitle );
			if ( empty( $sPageTitle ) ) {
				continue;
			}

			$oTitle = Title::newFromText( $sPageTitle );
			if ( $oTitle instanceof Title === false ) {
				$this->error( "$sPageTitle is not a valid page title!" );
				continue;
			}
			$aPageIds[$oTitle->getArticleID()] = true;
		}

		$dbr = $this->getDB( DB_REPLICA );
		$res = $dbr->select(
			'imagelinks',
			'il_to',
			[
				'il_from' => array_keys( $aPageIds )
			],
			__METHOD__
		);

		$aFiles = [];
		$repoGroup = MediaWikiServices::getInstance()->getRepoGroup();
		foreach ( $res as $row ) {
			$oFile = $repoGroup->findFile( Title::makeTitle( NS_FILE, $row->il_to ) );
			if ( $oFile instanceof File === false ) {
				$this->error( "'{$row->il_to}' does not exist!" );
				continue;
			}
			$aFiles[$row->il_to] = $oFile;
		}

		foreach ( $aFiles as $sInternalFileName => $oFile ) {
			$sDestPath = "$sDest/$sInternalFileName";
			if ( !$oFile->exists() ) {
				$this->error( "$sInternalFileName does not exist!" );
			}

			$sSourcePath = $oFile->getRepo()->getBackend()->getLocalReference( [
				'src' => $oFile->getPath(),
				'latest' => true
			] )->getPath();

			MediaWikiServices::getInstance()->getHookContainer()->run(
				'BSExportFilesBeforeSave',
				[
					$this,
					&$oFile,
					&$sDestPath,
					&$sSourcePath
				]
			);

			$this->output( "Exporting '$sInternalFileName to '$sDestPath' ..." );

			$bSuccess = copy( $sSourcePath, $sDestPath );
			if ( $bSuccess === false ) {
				$this->output( "FAILED" );
			} else {
				$this->output( "SUCCESS" );
			}
		}
	}
}

$maintClass = BSExportFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
