<?php

require_once __DIR__ . '/BSBatchFileProcessorBase.php';

use MediaWiki\MediaWikiServices;

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

		Hooks::run( 'BSImportFilesMakeTitle', [ $this, &$oTargetTitle, &$oRepo, $aParts, $sRoot ] );
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
			$props = FSFile::getPropsFromPath( $oFile->getPathname() );
			$flags = 0;
			$publishOptions = [];
			$handler = MediaHandler::getHandler( $props['mime'] );
			if ( $handler ) {
				$publishOptions['headers'] = $handler->getStreamHeaders( $props['metadata'] );
			} else {
				$publishOptions['headers'] = [];
			}
			$archive = $oRepoFile->publish( $oFile->getPathname(), $flags, $publishOptions );
			if ( !$archive->isGood() ) {
				$this->error( "failed. (" . $archive->getWikiText() . ")" );
			}
		}

		$commentText = SpecialUpload::getInitialPageText( $commentText, '' );
		$summary = $this->getOption( 'summary', '' );

		if ( $this->hasOption( 'dry' ) ) {
			$this->output( "done." );
		} elseif ( $oRepoFile->recordUpload2( $archive->value, $summary, $commentText, $props, false ) ) {
			$this->output( "done." );
		}

		if ( $this->hasOption( 'verbose' ) ) {
			$this->output( "Canonical Filename: {$oRepoFile->getName()}" );
			$this->output( "Canonical URL: {$oRepoFile->getCanonicalUrl()}" );
		}

		return true;
	}
}

$maintClass = 'BSImportFiles';
require_once RUN_MAINTENANCE_IF_MAIN;
