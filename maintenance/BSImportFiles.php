<?php

require_once __DIR__ . '/BSBatchFileProcessorBase.php';

use MediaWiki\MediaWikiServices;
use MediaWiki\Specials\SpecialUpload;
use MediaWiki\Title\Title;

class BSImportFiles extends BSBatchFileProcessorBase {

	protected $sBasePath = '';

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption( 'overwrite', 'Overwrite existing files?' );
		$this->addOption( 'dry', 'Dry run. Do not actually upload files to the repo' );
		$this->addOption( 'summary', 'A summary for all file uploads' );
		$this->addOption( 'comment', 'A comment for all file uploads' );
		$this->addOption( 'verbose', 'More verbose output' );
		$this->deleteOption( 'dest' );
	}

	/**
	 *
	 */
	public function execute() {
		$this->sBasePath = str_replace( '\\', '/', realpath( $this->getOption( 'src' ) ) );
		parent::execute();
	}

	/**
	 * @return array
	 */
	public function getFileList() {
		$aFiles = parent::getFileList();
		$aFilteredFiles = [];
		foreach ( $aFiles as $sFilePath => $oFile ) {
			if ( !$oFile->isDir() ) {
				$aFilteredFiles[$sFilePath] = $oFile;
			}
		}
		ksort( $aFilteredFiles, SORT_NATURAL );
		return $aFilteredFiles;
	}

	/**
	 *
	 * @param SplFileInfo $oFile
	 * @return bool
	 */
	public function processFile( $oFile ) {
		$sRelPath = str_replace(
			$this->sBasePath,
			'',
			str_replace( '\\', '/', $oFile->getRealPath() )
		);
		$sRelPath = trim( $sRelPath, '/' );

		$aParts = explode( '/', $sRelPath );
		$sRoot = $aParts[0];
		$sTitle = implode( '_', $aParts );

		// MediaWiki normalizes multiple spaces/undescores into one single score/underscore
		$sTitle = str_replace( ' ', '_', $sTitle );
		$sTitle = preg_replace( '#(_)+#si', '_', $sTitle );
		$aParts = explode( '_', $sTitle );

		$oTargetTitle = Title::makeTitle( NS_FILE, $sTitle );
		$oRepo = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();

		MediaWikiServices::getInstance()->getHookContainer()->run( 'BSImportFilesMakeTitle', [
			$this,
			&$oTargetTitle,
			&$oRepo,
			$aParts,
			$sRoot
		] );
		$this->output( "Using target title {$oTargetTitle->getPrefixedDBkey()}" );

		$oRepoFile = $oRepo->newFile( $oTargetTitle );
		if ( $oRepoFile->exists() ) {
			if ( !$this->hasOption( 'overwrite' ) ) {
				$this->output( "File '{$oRepoFile->getName()}' already exists. Skipping..." );
				return true;
			} else {
				$this->output( "File '{$oRepoFile->getName()}' already exists. Overwriting..." );
			}
		}

		/*
		 * The following code is almost a dirext copy of
		 * <mediawiki>/maintenance/importImages.php
		 */
		$commentText = $this->getOption( 'comment', '' );

		if ( !$this->hasOption( 'dry' ) ) {
			$mwProps = new MWFileProps( MediaWiki\MediaWikiServices::getInstance()->getMimeAnalyzer() );
			$props = $mwProps->getPropsFromPath( $oFile->getPathname(), true );
			$flags = 0;
			$publishOptions = [];
			$handler = MediaHandler::getHandler( $props['mime'] );
			if ( $handler ) {
				if ( is_array( $props['metadata'] ) ) {
					$metadata = $props['metadata'];
				} else {
					$metadata = \Wikimedia\AtEase\AtEase::quietCall( 'unserialize', $props['metadata'] );
				}

				$publishOptions['headers'] = $handler->getContentHeaders( $metadata );
			} else {
				$publishOptions['headers'] = [];
			}
			$archive = $oRepoFile->publish( $oFile->getPathname(), $flags, $publishOptions );
			if ( !$archive->isGood() ) {
				$this->output( "failed. (" .
					$archive->getMessage( false, false, 'en' )->text() .
					")\n" );
			}
			$user = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
				->getMaintenanceUser()->getUser();
		}

		$commentText = SpecialUpload::getInitialPageText( $commentText, '' );
		$summary = $this->getOption( 'summary', '' );

		if ( $this->hasOption( 'dry' ) ) {
			$this->output( "done." );
		} elseif ( $oRepoFile->recordUpload3( $archive->value, $summary, $commentText, $user, $props ) ) {
			$this->output( "done." );
		}

		if ( $this->hasOption( 'verbose' ) ) {
			$this->output( "Canonical Filename: {$oRepoFile->getName()}" );
			$this->output( "Canonical URL: {$oRepoFile->getCanonicalUrl()}" );
		}

		return true;
	}
}

$maintClass = BSImportFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
