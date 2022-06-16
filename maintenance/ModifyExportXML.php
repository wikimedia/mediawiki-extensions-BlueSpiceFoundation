<?php

require_once 'BSMaintenance.php';

class ModifyExportXML extends BSMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption( 'input', 'The file to read from', true, true );
		$this->addOption( 'output', 'The file to write to', true, true );
		$this->addOption(
				'newtitlemap',
			'A file with page titles to be used. MUST MATCH `newtitlemap` BY LINE NUMBER',
			true,
				true
		);
		$this->addOption(
			'oldtitlemap',
			'A file with page titles to be replaced. MUST MATCH `newtitlemap` BY LINE NUMBER',
			true,
			true
		);

		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	protected $aTitles = [];

	/** @var array */
	private $titleMap = [];

	public function execute() {
		$sInputFilePath    = $this->getOption( 'input' );
		$sOutputFilePath   = $this->getOption( 'output' );
		$this->initTitleMap();

		$oDOMDoc = new DOMDocument( '1.0', 'UTF-8' );
		$oDOMDoc->load( $sInputFilePath );

		$this->output( 'Removing <siteinfo> tag(s)...' );
		$oSiteInfoElements = $oDOMDoc->getElementsByTagName( "siteinfo" );
		foreach ( $oSiteInfoElements as $oSiteInfoElement ) {
			$oSiteInfoElement->parentNode->removeChild( $oSiteInfoElement );
			$this->output( '... done.' );
		}

		$this->output( 'Modifying titles...' );
		$oTitleElements = $oDOMDoc->getElementsByTagName( "title" );
		foreach ( $oTitleElements as $oTitleElement ) {
			$oldTitle = trim( $oTitleElement->nodeValue );
			$oldTitle = str_replace( ' ', '_', $oldTitle );
			if ( isset( $this->titleMap[$oldTitle] ) ) {
				$newTitle = $this->titleMap[$oldTitle];
				$oTitleElement->removeChild( $oTitleElement->firstChild );
				$oTitleElement->appendChild( $oDOMDoc->createTextNode( $newTitle ) );
				$this->output( "'$oldTitle' -->'$newTitle'" );
			}
		}

		$this->output( 'Modifying revision texts...' );
		$oRevisionTextElements = $oDOMDoc->getElementsByTagName( "text" );
		foreach ( $oRevisionTextElements as $oRevisionTextElement ) {
			$sText = trim( $oRevisionTextElement->textContent );
			if ( strlen( $sText ) == 0 ) {
				$iRevisionId  = $oRevisionTextElement->parentNode
					->getElementsByTagName( 'id' )->item( 0 )->textContent;
				$sArticleName = $oRevisionTextElement->parentNode->parentNode
					->getElementsByTagName( 'title' )->item( 0 )->textContent;
				$this->output( '<text> element in Revision id=' . $iRevisionId
				. ' (Page "' . $sArticleName . '") contains no text.' );
				continue;
			}
			$sText = preg_replace_callback( '#\[\[(.*?)\]\]#si', [ $this, 'modifyWikiLink' ], $sText );
			$oRevisionTextElement->removeChild( $oRevisionTextElement->firstChild );
			$oRevisionTextElement->appendChild( $oDOMDoc->createTextNode( $sText ) );
		}

		$this->output( 'Modifying comments...' );
		$oRevisionCommentElements = $oDOMDoc->getElementsByTagName( "comment" );
		foreach ( $oRevisionCommentElements as $oRevisionCommentElement ) {
			$sComment = trim( $oRevisionCommentElement->textContent );
			if ( strlen( $sComment ) == 0 ) {
				$iRevisionId  = $oRevisionTextElement->parentNode
					->getElementsByTagName( 'id' )->item( 0 )->textContent;
				$sArticleName = $oRevisionTextElement->parentNode->parentNode
					->getElementsByTagName( 'title' )->item( 0 )->textContent;
				$this->output( '<comment> element in Revision id=' . $iRevisionId
					. ' (Page "' . $sArticleName . '") contains no text.' );
				continue;
			}
			$sComment = preg_replace_callback( '#\[\[(.*?)\]\]#si', [ $this, 'modifyWikiLink' ], $sComment );
			$oRevisionCommentElement->removeChild( $oRevisionCommentElement->firstChild );
			$oRevisionCommentElement->appendChild( $oDOMDoc->createTextNode( $sComment ) );
		}

		$vWrittenBytes = $oDOMDoc->save( $sOutputFilePath );

		if ( $vWrittenBytes === false ) {
			$this->output( 'An error occurred. Output file could not be saved.' );
		} else {
			$this->output( "Success. $vWrittenBytes Bytes have been written to '$sOutputFilePath'" );
		}
	}

	private function modifyWikiLink( $aMatches ) {
		$aLinkParts = explode( '|', $aMatches[1], 2 );
		$sLinkPart  = trim( $aLinkParts[0] );

		$oldTitle = str_replace( ' ', '_', $sLinkPart );
		if ( !isset( $this->titleMap[$oldTitle] ) ) {
			$this->output( 'Skipping "' . $aMatches[0] . '". Key "' . $sLinkPart . '" was not found.' );
			// Only modify links that have changed.
			return $aMatches[0];
		}
		$sNewWikiLink = '[[' . $this->titleMap[ $oldTitle ];
		if ( isset( $aLinkParts[1] ) ) {
			// Add optional description text
			$sNewWikiLink .= '|' . $aLinkParts[1];
		}
		$sNewWikiLink .= ']]';
		$this->output( '"' . $aMatches[0] . '" --> "' . $sNewWikiLink . '"' );
		return $sNewWikiLink;
	}

	private function initTitleMap() {
		$newTitleMapContent = file_get_contents( $this->getOption( 'newtitlemap' ) );
		$oldTitleMapContent = file_get_contents( $this->getOption( 'oldtitlemap' ) );

		$newTitleMapLines = explode( "\n", $newTitleMapContent );
		$oldTitleMapLines = explode( "\n", $oldTitleMapContent );

		if ( count( $newTitleMapLines ) !== count( $oldTitleMapLines ) ) {
			throw new Exception( "Maps must have same number of lines!" );
		}

		for ( $i = 0; $i < count( $oldTitleMapLines ); $i++ ) {
			$newTitle = trim( $newTitleMapLines[$i] );
			$oldTitle = trim( $oldTitleMapLines[$i] );

			// Normalize
			$newTitle = str_replace( ' ', '_', $newTitle );
			$oldTitle = str_replace( ' ', '_', $oldTitle );

			if ( isset( $this->titleMap[$oldTitle] ) ) {
				throw new Exception( "A title must not occur twice in the map: $oldTitle" );
			}

			$this->titleMap[$oldTitle] = $newTitle;
		}
	}
}

$maintClass = ModifyExportXML::class;
require_once RUN_MAINTENANCE_IF_MAIN;
