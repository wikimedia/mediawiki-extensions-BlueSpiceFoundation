<?php

require_once 'BSMaintenance.php';

class BSExtractPageTitles extends BSMaintenance {

	/**
	 *
	 * @var string[]
	 */
	private $errors = [];

	/**
	 *
	 * @var string[]
	 */
	private $titles = [];

	/**
	 *
	 * @var string
	 */
	private $destPath = '';

	public function __construct() {
		$this->addOption( 'srcxml', 'The path to the source file', true, true );

		parent::__construct();
	}

	public function execute() {
		$srcXmlPath = $this->getOption( 'srcxml' );

		$this->output( "Processing $srcXmlPath\n" );

		$dom = new DOMDocument();
		$dom->load( $srcXmlPath );
		$dom->recover = true;

		$pageEls = $dom->getElementsByTagName( 'page' );
		foreach ( $pageEls as $pageEl ) {
			$titleEl = $pageEl->getElementsByTagName( 'title' )->item( 0 );
			$title = $titleEl->nodeValue;
			$this->output( "Found '$title'" );
			$this->titles[] = Title::newFromText( $title )->getPrefixedDBkey();

		}
		$noOfTitles = count( $this->titles );
		natsort( $this->titles );

		file_put_contents( $this->destPath . '/pages.txt', implode( "\n", $this->titles ) );

		$this->output( "Found '$noOfTitles' titles!" );
	}
}

$maintClass = 'BSExtractPageTitles';
require_once RUN_MAINTENANCE_IF_MAIN;
