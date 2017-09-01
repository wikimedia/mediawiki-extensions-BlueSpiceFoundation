<?php

require_once( __DIR__.'/BSMaintenance.php' );

class BSMassEditBase extends BSMaintenance {
	protected $aFileMap = array();
	protected $aPageNameMap = array();

	protected $iTitleCount = 0;
	protected $iModifiedTitleCount = 0;
	protected $iFailureTitleCount = 0;

	public function __construct() {
		parent::__construct();
		$this->addOption( 'dry', 'Do not really modify contents' );
		$this->addOption( 'verbose', 'Whether or not to output additional information', false, false );
	}

	public function execute() {
		$aTitles = $this->getTitleList();
		$this->iTitleCount = count( $aTitles );

		foreach( $aTitles as $sTitle => $oTitle ) {
			if( $oTitle instanceof Title === false ) {
				$this->error( "Invalid Title '$sTitle'!" );
				continue;
			}
			$oWikiPage = WikiPage::factory( $oTitle );
			$this->output( "\nModifying '{$oTitle->getPrefixedText()}'..." );
			$oNewContent = $this->modifyContent( $oWikiPage->getContent(), $oWikiPage );
			if( $oNewContent instanceof Content === false ) {
				$this->output( "--> Content of page '{$oTitle->getPrefixedText()}' has NOT been modified!" );
				continue;
			}

			if( $this->isDryMode() ) {
				continue;
			}

			$oStatus = $oWikiPage->doEditContent(
				$oNewContent,
				$this->getEditSummay( $oWikiPage )
			);
			if( !$oStatus->isOK() ) {
				$this->error( "--> Content of page {$oTitle->getPrefixedText()} could not be modified: {$oStatus->getMessage()->plain()}");
				$this->iFailureTitleCount++;
			}
			else {
				$this->output( "--> Content of page {$oTitle->getPrefixedText()} has successfully been modified" );
				$this->iModifiedTitleCount++;
			}
		}

		$this->output( 'Processed Titles: '.$this->iTitleCount );
		$this->output( 'Modified contents: '.$this->iModifiedTitleCount );
		$this->output( 'Failed modifications: '.$this->iFailureTitleCount );
	}

	/**
	 * Provides all the titles the edit should be performed on
	 * @return array of Title objects
	 */
	protected function getTitleList() {
		$dbr = $this->getDB( DB_REPLICA );
		$aTitles = array();

		$res = $dbr->select( 'page', '*' );
		foreach( $res as $row ) {
			$oTitle = Title::newFromRow( $row );
			$aTitles[ $oTitle->getPrefixedDBkey() ] = $oTitle;
		}

		return $aTitles;
	}

	public function isDryMode() {
		return (bool)$this->getOption( 'dry', false );
	}

	/**
	 * Can be overwritten by subclass if other content model than WikiText should be processed
	 * @param Content $oContent
	 * @param WikiPage $oWikiPage
	 * @return Content
	 */
	protected function modifyContent( $oContent, $oWikiPage ) {
		$sTextContent = '';
		if( $oContent instanceof Content ) {
			$sTextContent = $oContent->getContentHandler()->getContentText( $oContent );
		}

		$sNewTextContent = $this->modifyTextContent(
			$sTextContent,
			$oWikiPage
		);

		if( $sTextContent === $sNewTextContent ) {
			return null; //No change? No edit!
		}

		return $oWikiPage->getContentHandler()->makeContent(
			$sNewTextContent,
			$oWikiPage->getTitle()
		);
	}

	/**
	 * Default method that gets called for actual modification of text
	 * repesenation of a wikipage content
	 * Can be overwritten by subclass if other than preg_replace_callback
	 * implementation should be used
	 * @param string $sContent
	 * @param WikiPage $oWikiPage
	 * @return string
	 */
	protected function modifyTextContent( $sContent, $oWikiPage ) {
		$sNewContent = preg_replace_callback(
			$this->getTextModificationRegexPatterns(),
			array( $this, 'textModificationCallback' ),
			$sContent
		);

		if( $this->isVerbose() ) {
			$this->outputDiff( $sContent, $sNewContent );
		}

		return $sNewContent;
	}

	/**
	 * To have a nice summary in revision history for the edit made
	 * @param WikiPage $oWikiPage
	 * @return string
	 */
	protected function getEditSummay( $oWikiPage ) {
		return "Modified by script '".  get_class( $this )."'";
	}

	/**
	 * Provides the regex for default implementation of "modifyTextContent"
	 * @return mixed string or array-of-strings containig a valid regular expression
	 */
	protected function getTextModificationRegexPatterns() {
		//Just an example: "Find all internal links in WikiText"
		return '#\[\[(.*?)\]\]#s';
	}

	/**
	 * Default callback for replacements based on
	 * 'getTextModificationRegexPatterns'
	 * Should probably be overwritten by subclass
	 * @param type $aMatches
	 * @return type
	 */
	public function textModificationCallback( $aMatches ) {
		//Just a dummy: returns unmodified
		return $aMatches[0];
	}

	protected function outputDiff( $sContent, $sNewContent ) {
		$oDiff = new Diff(
			explode( "\n", $sContent ),
			explode( "\n", $sNewContent )
		);
		if( !$oDiff->isEmpty() ) {
			$oDiffFormatter = new UnifiedDiffFormatter();
			$this->output(
				$oDiffFormatter->format( $oDiff )
			);
			$this->output( "---\n" );
		}
	}

	public function isVerbose() {
		return (bool)$this->getOption( 'verbose', false );
	}

}
