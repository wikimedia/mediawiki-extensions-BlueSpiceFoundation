<?php

require_once( 'BSMaintenance.php' );

class ModifyExportXML extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption('input', 'The file to read from', true, true);
		$this->addOption('output', 'The file to write to', true, true);
		$this->addOption('newnamespacetext', 'The namespace text to prepend', true, true);
		$this->addOption('oldnamespacetext', 'The old namespace text', false, true);
		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	protected $aTitles = array();
	public function execute() {
		$sInputFilePath    = $this->getOption( 'input' );
		$sOutputFilePath   = $this->getOption( 'output' );
		$sNamespaceText    = $this->getOption( 'newnamespacetext' );
		$sOldNamespaceText = $this->getOption( 'oldnamespacetext' );
		$sOldNamespaceText = strtoupper( $sOldNamespaceText );

		$oDOMDoc = new DOMDocument( '1.0', 'UTF-8' );
		$oDOMDoc->load( $sInputFilePath );

		echo "\n".'Removing <siteinfo> tag(s)...'."\n";
		$oSiteInfoElements = $oDOMDoc->getElementsByTagName( "siteinfo" );
		foreach( $oSiteInfoElements as $oSiteInfoElement ) {
			$oSiteInfoElement->parentNode->removeChild( $oSiteInfoElement );
			echo 'done.'."\n";
		}

		echo "\n".'Modifying titles...'."\n";
		$oTitleElements = $oDOMDoc->getElementsByTagName( "title" );
		foreach( $oTitleElements as $oTitleElement ) {
			//HINT: http://www.php.net/manual/de/class.domnode.php#95545
			$sOldTitle = trim( $oTitleElement->textContent );
			$sNewTitle = $sOldTitle;
			$aTitleParts = explode( ":", $sOldTitle, 2 );
			$aPotentialNamespaceText = strtoupper( trim( $aTitleParts[0] ) );
			if( $aPotentialNamespaceText == $sOldNamespaceText ) {
				$sNewTitle = $sNamespaceText.':'.$aTitleParts[1];
			}
			else {
				$sNewTitle = $sNamespaceText.':'.$sOldTitle;
			}
			$this->aTitles[$sOldTitle] = $sNewTitle;
			$oTitleElement->removeChild( $oTitleElement->firstChild );
			$oTitleElement->appendChild( $oDOMDoc->createTextNode( $sNewTitle ) );
			echo '"'.$sOldTitle.'" --> "'.$sNewTitle.'"'."\n";
		}

		echo "\n".'Modifying revision texts...'."\n";
		$oRevisionTextElements = $oDOMDoc->getElementsByTagName( "text" );
		foreach( $oRevisionTextElements as $oRevisionTextElement ) {
			$sText = trim( $oRevisionTextElement->textContent );
			if( strlen( $sText ) == 0 ) {
				$iRevisionId  = $oRevisionTextElement->parentNode->getElementsByTagName('id')->item(0)->textContent;
				$sArticleName = $oRevisionTextElement->parentNode->parentNode->getElementsByTagName('title')->item(0)->textContent;
				echo '<text> element in Revision id='.$iRevisionId.' (Article "'.$sArticleName.'") contains no text.'."\n";
				continue;
			}
			$sText = preg_replace_callback( '#\[\[(.*?)\]\]#si', array( $this, 'modifyWikiLink'), $sText );
			$oRevisionTextElement->removeChild( $oRevisionTextElement->firstChild );
			$oRevisionTextElement->appendChild( $oDOMDoc->createTextNode( $sText ) );
		}

		echo "\n".'Modifying comments...'."\n";
		$oRevisionCommentElements = $oDOMDoc->getElementsByTagName( "comment" );
		foreach( $oRevisionCommentElements as $oRevisionCommentElement ) {
			$sComment = trim( $oRevisionCommentElement->textContent );
			if( strlen( $sComment ) == 0 ) {
				$iRevisionId  = $oRevisionTextElement->parentNode->getElementsByTagName('id')->item(0)->textContent;
				$sArticleName = $oRevisionTextElement->parentNode->parentNode->getElementsByTagName('title')->item(0)->textContent;
				echo '<comment> element in Revision id='.$iRevisionId.' (Article "'.$sArticleName.'") contains no text.'."\n";
				continue;
			}
			$sComment = preg_replace_callback( '#\[\[(.*?)\]\]#si', array( $this, 'modifyWikiLink'), $sComment );
			$oRevisionCommentElement->removeChild( $oRevisionCommentElement->firstChild );
			$oRevisionCommentElement->appendChild( $oDOMDoc->createTextNode( $sComment ) );
		}

		$vWrittenBytes = $oDOMDoc->save( $sOutputFilePath );
		//Alternative to prevent DOMDocument from rewriting all unicode chars as entities. But: seems to be unnecessary, due to the MediaWiki import.
		//$sXML = $oDOMDoc->saveXML();
		//$vWrittenBytes = file_put_contents( $sOutputFilePath, html_entity_decode( $sXML ) );

		if( $vWrittenBytes === false ){
			echo 'An error occurred. Output file could not be saved.'."\n";
		}
		else {
			echo 'Success. '.$vWrittenBytes.' Bytes have been written to "'.$sOutputFilePath.'".'."\n";
		}
	}

	private function modifyWikiLink( $aMatches ) {
		$aLinkParts = explode( '|', $aMatches[1], 2 );
		$sLinkPart  = trim ($aLinkParts[0] );
		if( !isset( $this->aTitles[$sLinkPart] ) ) {
			echo 'Skipping "'. $aMatches[0].'". Key "'.$sLinkPart.'" was not found.'."\n";
			return $aMatches[0]; //Only modify links that have changed.
		}
		$sNewWikiLink = '[['.$this->aTitles[ $sLinkPart ];
		if( isset( $aLinkParts[1] ) ) $sNewWikiLink .= '|'.$aLinkParts[1]; //Add optional description text
		$sNewWikiLink .= ']]';
		echo '"'.$aMatches[0].'" --> "'.$sNewWikiLink.'"'."\n";
		return $sNewWikiLink;
	}
}

$maintClass = 'ModifyExportXML';
require_once RUN_MAINTENANCE_IF_MAIN;
