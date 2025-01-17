<?php

use MediaWiki\Title\Title;

require_once 'BSMaintenance.php';

class BSExtractPageTitles extends BSMaintenance {

	/**
	 *
	 * @var string[]
	 */
	private $titles = [];

	public function __construct() {
		parent::__construct();

		$this->addOption( 'srcxml', 'The path to the source file', true, true );
		$this->addOption( 'dest', 'The path to the destination file', true, true );
	}

	public function execute() {
		$srcXmlPath = $this->getOption( 'srcxml' );
		$destPath = $this->getOption( 'dest' );

		$this->output( "Processing $srcXmlPath\n\n" );

		$dom = new DOMDocument();
		$dom->load( $srcXmlPath );
		$dom->recover = true;

		$pageEls = $dom->getElementsByTagName( 'page' );
		foreach ( $pageEls as $pageEl ) {
			$titleEl = $pageEl->getElementsByTagName( 'title' )->item( 0 );
			$title = $titleEl->nodeValue;
			$this->output( "Found '$title'" );
			$titleObj = Title::newFromText( $title );
			$preFixedTitle = $titleObj->getPrefixedDBkey();

			// We add an explicit "empty prefix" for the main namespace, to ease further
			// processing, as this script is likely to be used as a base for `ModifyExportXML`
			if ( $titleObj->getNamespace() === NS_MAIN ) {
				$preFixedTitle = ':' . $preFixedTitle;
			}

			$this->titles[] = $preFixedTitle;

		}
		$noOfTitles = count( $this->titles );
		natsort( $this->titles );

		file_put_contents( $destPath, implode( "\n", $this->titles ) );

		$this->output( "\nTotal of '$noOfTitles' titles found!" );
	}
}

$maintClass = BSExtractPageTitles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
