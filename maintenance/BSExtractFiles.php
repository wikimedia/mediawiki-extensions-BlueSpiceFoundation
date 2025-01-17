<?php

require_once 'BSMaintenance.php';

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class BSExtractFiles extends BSMaintenance {

	/**
	 *
	 * @var string[]
	 */
	private $errors = [];

	/**
	 *
	 * @var array
	 */
	private $sourceImages = [];

	/**
	 *
	 * @var int
	 */
	private $noOfFiles = 0;

	/**
	 *
	 * @var string
	 */
	private $destPath = '';

	public function __construct() {
		parent::__construct();

		$this->addOption( 'srcxml', 'The path to the source file', true, true );
		$this->addOption( 'srcimages', 'The path to the source images directory', true, true );
		$this->addOption( 'dest', 'The folder to copy the files to', true, true );
	}

	public function execute() {
		$srcXmlPath = $this->getOption( 'srcxml' );
		$srcImagesPath = $this->getOption( 'srcimages' );
		$this->destPath = $this->getOption( 'dest' );

		$this->output( "Processing $srcXmlPath\n" );
		wfMkdirParents( $this->destPath );
		$this->readInSourceImages( $srcImagesPath );

		$dom = new DOMDocument();
		$dom->load( $srcXmlPath );
		$dom->recover = true;

		$pageEls = $dom->getElementsByTagName( 'page' );
		foreach ( $pageEls as $pageEl ) {
			$titleEl = $pageEl->getElementsByTagName( 'title' )->item( 0 );
			$title = $titleEl->nodeValue;
			$this->output( "Extract from $title ..." );
			$revisionEls = $pageEl->getElementsByTagName( 'revision' );
			foreach ( $revisionEls as $revisionEl ) {
				$textEl = $revisionEl->getElementsByTagName( 'text' )->item( 0 );
				$wikiText = $textEl->nodeValue;
				preg_replace_callback( '#\[\[(.*?)\]\]#si', function ( $matches ) {
					$parts = explode( '|', $matches[1] );
					$targetTitleText = $parts[0];
					$targetTitle = Title::newFromText( $targetTitleText );
					if ( $targetTitle instanceof Title === false ) {
						$this->errors[] = "No title for '{$matches[0]}]'!";
						return $matches[0];
					}
					if ( in_array( $targetTitle->getNamespace(), [ NS_FILE, NS_MEDIA ] ) ) {
						$this->addFileToExtract( $targetTitle );
					}
					return $matches[0];
				}, $wikiText );

			}
		}

		if ( !empty( $this->errors ) ) {
			$this->output( "Errors:" );
			foreach ( $this->errors as $error ) {
				$this->output( "* $error" );
			}
		}

		$this->output( "Extracted {$this->noOfFiles} file(s)!" );
	}

	/**
	 *
	 * @param Title $title
	 */
	private function addFileToExtract( $title ) {
		$file = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo()->newFile( $title );
		$fileName = $title->getDBkey();
		if ( !isset( $this->sourceImages[$fileName] ) ) {
			$fileName = ucfirst( $fileName );
			if ( !isset( $this->sourceImages[$fileName] ) ) {
				$this->errors[] = "File {$fileName} not found in source directory!";
				return;
			}
		}
		$path = $this->sourceImages[$fileName];
		$this->output( "* {$title->getDBkey()} ($path)" );
		$result = copy( $path, $this->destPath . '/' . basename( $path ) );
		if ( $result === false ) {
			die( "Could not copy '$path'!" );
		}
		$this->noOfFiles++;
	}

	private function readInSourceImages( $srcImagesPath ) {
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $srcImagesPath ),
			RecursiveIteratorIterator::SELF_FIRST
		);

		$this->sourceImages = [];
		foreach ( $files as $path => $file ) {
			$file instanceof SplFileInfo;
			if ( !$file->isFile() ) {
				continue;
			}
			$basename = $file->getBasename();
			$title = Title::makeTitle( NS_FILE, $basename );
			$wikiFileName = $title->getDBkey();

			if ( isset( $this->sourceImages[$wikiFileName] ) ) {
				die( "Duplicate '$wikiFileName' in source directory! This must never happen!" );
			}
			$this->sourceImages[$wikiFileName] = $file->getPathname();
		}
	}

}

$maintClass = BSExtractFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
