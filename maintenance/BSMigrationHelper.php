<?php
/**
 * Maintenance scripthelper for contentmigration from word .htm documents
 *
 * @file
 * @ingroup Maintenance
 * @author Robert Vogel
 * @author Stefan Widmann
 * @licence GNU General Public Licence 2.0 or later
 */

class BSMigrationHelper{
//		public $aArticles  = array();
		public $sMainArticleName = '';
		public $aFiles     = array();
		public $sBookTitle = '';
		public $sSourceFolderRealPath = '';
		public $sSavePath = null;
//		public $sCurrentSourceFileName = '';
		public $sCurrentTargetArticleName = '';
		public $numberOfArticlesCreated = 0;
		public $oResultDocument = null;
		public $aFilesCreated = array();
		public $aFilesNotCreated = array();
		public $iImageCount = 0;
		public $aSkipHeadlines = array( // values only lowercase
			'inhaltsverzeichnis',
		);
//		public $sourceFolderBaseName = '';
	
	public function __construct () {
		$this->oResultDocument = new DOMDocument();
		$this->oResultDocument->loadXML('<mediawiki></mediawiki>');
		$this->oResultDocument->formatOutput = true;
	}
		
		public function processDocument( $oDOC, &$sWikiText ) {
		//Collect some MetaData
		$oXPath = new DOMXPath($oDOC);
		//$oBookNameNode = $oXPath->query( '/html/body/div/p[3]')->item(0);
		//$this->output( $oBookNameNode->nodeValue);
		//$oBookPartNode = $oXPath->query( '/html/body/div/p[5]')->item(0);
		//$this->output( $oBookPartNode->nodeValue);
		$oArticleNameNode = $oXPath->query( '/html/body/div/p[7]')->item(0);
		$sCleanedArticleName = '';
		if( $oArticleNameNode instanceof DOMElement )
			$sCleanedArticleName = str_replace(array( "\n", "\r", "\t" ), '', $oArticleNameNode->nodeValue);

		if( empty( $sCleanedArticleName ) ) {
			$this->output( 'No ArticleName found!' );
			//$sWikiText = $oDOC->saveXML();
			//return;
		}
		//$this->output( '--------------X: '.$sCleanedArticleName );
		$oTitle = Title::newFromText( $sCleanedArticleName );
		if( $oTitle instanceof Title ) {
			$this->output( 'ArticleName found!' );
			$this->sCurrentTargetArticleName = $oTitle->getDBkey();
			//$sWikiText = $oDOC->saveXML();
			//return;
		}
		$this->output( $this->sCurrentTargetArticleName );
		//$oRevisionNode = $oXPath->query( '/html/body/div/p[15]')->item(0);
		//$this->output( $oRevisionNode->nodeValue);
		//$oDateNode = $oXPath->query( '/html/body/div/p[17]')->item(0);
		//$this->output( $oDateNode->nodeValue);
		
		//Process the body content
		$oBody = $oDOC->getElementsByTagName('body')->item(0);
		foreach( $oBody->childNodes as $oBodyElement ) {
			if( $oBodyElement instanceof DOMElement == false ) continue;
			
			if( strpos( $oBodyElement->getAttribute('class'), 'WordSection' ) !== false ){
				$this->output('Processing WordSection "'.$oBodyElement->getAttribute('class') ).'"';
				$this->processWordSection( $oBodyElement, $sWikiText );
			}else {
				//$this->output( 'Adding pagebreak');
				//$sWikiText .= '<bs:uepagebreak />'."\n";
			}
		}
	}
	
	/**
	 * 
	 * @param DOMElement $oBodyElement
	 * @param string $sWikiText
	 */
	public function processWordSection( $oWordSection, &$sWikiText ) {
		if( $oWordSection instanceof DOMElement == false ) return;
		wfRunHooks( 'BSMigrationHelperBeforeProcessingWordSection', array( $this, $oWordSection, &$sWikiText) );
		foreach( $oWordSection->childNodes as $oWSElement ) {
			$this->processWordSectionElement( $oWSElement, $sWikiText );
		}
	}
	
	public function processWordSectionElement( $oWSElement, &$sWikiText ) {
		//$sWikiText = preg_replace('/&#x.*?;/', '', $sWikiText);
		if( $oWSElement instanceof DOMElement == false ) return;
		if( in_array( $oWSElement->nodeName, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6') ) ) {
			$sHeading = str_replace( array("\n", "\r" ), array(' ', ''), $oWSElement->nodeValue);
			$sHeading = preg_replace('#^\d(\.\d)*#', '', $sHeading);
			$sHeading = str_replace("\xc2\xa0", '', $sHeading);
			$sHeading = trim( $sHeading );
//			$count = 0;
//			file_put_contents( '/tmp/headline.txt', $sHeading."\n\n" , FILE_APPEND);
			//file_put_contents( '/tmp/headline', $sHeading."\n" );
			$sHeading = preg_replace( '# ?(\d+)?\.(\d+) #', '', $sHeading );
			$sHeading = preg_replace( '#^[0-9]+ #', '', $sHeading );
			$sHeading = preg_replace( '#^\d. |^\. #', '', $sHeading );
			
			
			if( empty( $sHeading ) ) return;
			if( in_array( strtolower( $sHeading ), $this->aSkipHeadlines ) ) {
				$this->output('Skipping headline "'.$sHeading.'" found in Array >>SkipHeadlines<<');
				return;
			}
//			file_put_contents( '/tmp/headline.txt', $sHeading."\n\n" , FILE_APPEND);
//			if( !$count )	$sHeading = preg_replace( '#^(-|§)[ \x{00A0}]+#u', '', $sHeading );

			$this->output('Processing headline "'.$sHeading.'"');
			$sMarkup = str_repeat('=', $oWSElement->nodeName[1] + 1 );
			$sHeading = "\n\n".$sMarkup.trim($sHeading).$sMarkup."\n\n";
			wfRunHooks( 'BSMigrationHelperProcessingHeadline', array( $this, $oWSElement, &$sHeading, &$sWikiText) );
			$sWikiText .= $sHeading;
			return;
		}

		if( $oWSElement->nodeName == 'p') {
			$sClass = $oWSElement->getAttribute( 'class' );
			wfRunHooks( 'BSMigrationHelperBeforeProcessingParagraph', array( $this, $oWSElement, $sClass, &$sWikiText) );
			if( $sClass == 'berschrift' ) {
				$this->output('Skipping class "berschrift": '.  str_replace(array("\n", "\r"), '', $oWSElement->nodeValue) );
				return;
			}
			if( strpos($sClass, 'MsoToc') !== false ) {
				$this->output('Skipping class "MsoToc*": '.  str_replace(array("\n", "\r"), '', $oWSElement->nodeValue) );
				return;
			}

			if( $sClass == 'Unterstrichen' ) {
				$sHeading = str_replace( array("\n", "\r", '&nbsp;'), array('', ' '), $oWSElement->nodeValue);
				$sWikiText .= "\n\n".'====='.$sHeading.'====='."\n\n";
				return;
			}

			if( strpos( $sClass, 'MsoListBullet') !== false ) {
				$iClassIncrement = str_replace('MsoListBullet', '', $sClass);
				if( empty( $iClassIncrement ) ) $iClassIncrement = 1;
				$sIndent = str_repeat( '*', $iClassIncrement );
				$sWikiText .= $sIndent;
			}

			if( $sClass == 'MsoListParagraphCxSpMiddle' ) {
				$sListItem = str_replace( array("\n", "\r", '&nbsp;'), ' ', $oWSElement->nodeValue);
var_dump( $sListItem );
				$sReplace = '*';
				$count = 0;
				//file_put_contents( '/tmp/listitem.txt', $sListItem."\n", FILE_APPEND );
				$sListItem = preg_replace( '#^\d+\.[ \x{00A0}]+#u', '', $sListItem, -1, $count );
				$sListItem = preg_replace( '#^ +.[ \x{00A0}]+#u', '', $sListItem );
//				$sListItem = preg_replace( '#[\x{00A0}]+#u', '', $sListItem );
				if( $count ) {
					$sReplace = '#';
				} else {
					$sListItem = preg_replace( '#^(-|§|  -)[ \x{00A0}]+#u', '', $sListItem);
					$sListItem = preg_replace( '#^ +.[ \x{00A0}]+#u', '', $sListItem);
					$sListItem = preg_replace( '#^([\x{00A0}]|[\x{00B7}])+#u', '', $sListItem );
				}
				$sWikiText .= "\n$sReplace ".$sListItem."";
				return;
			}

			if( $sClass == 'MsoListParagraphCxSpMiddle2' ) {
				$sListItem = str_replace( array("\n", "\r", '&nbsp;'), array('', ' '), $oWSElement->nodeValue);

				$sReplace = '**';
				$count = 0;
				$sListItem = preg_replace( '#^\d+\.[ \x{00A0}]+#u', '', $sListItem, -1, $count );
				if( $count ) {
					$sReplace = '##';
				} else {
					$sListItem = preg_replace( '#^(-|§| -)[ \x{00A0}]+#u', '', $sListItem);
				}
				$sWikiText .= "$sReplace ".$sListItem."\n";

				return;
			}

			$this->processInline( $oWSElement, $sWikiText );
			//$sWikiText .= "\n";
			$sWikiText = trim( $sWikiText );
			
			if( $sClass == 'MsoNormal' 
					|| strpos( $sClass, 'Deckblatt') !== false 
					|| strpos( $sClass, 'MsoListBullet') !== false ) {
				$sWikiText .= "\n\n";
			}
		}

		if( $oWSElement->nodeName == 'table' ) {
			$this->processTable( $oWSElement, $sWikiText );
		}
	}
	
	public function processInline( $oWSElement, &$sWikiText ) {
		$sWikiText = str_replace( "\n ", "\n", $sWikiText );

		foreach( $oWSElement->childNodes as $oChild ){
			if( $oChild instanceof DOMText ) {
				$sNodeValue = str_replace( array("\n", "\r"), array(' ', ''), $oChild->nodeValue );
				$sNodeValue = str_replace("\xc2\xa0", '', $sNodeValue);
				$sNodeValue = str_replace("\xa0", '', $sNodeValue);
//				$sNodeValue = str_replace("\xb7", '', $sNodeValue);
//				$sNodeValue = preg_replace('/[\x00-\x1f]/', '?', $sNodeValue);
				$sNodeValue = preg_replace('/&#x.*?;/', '', $sNodeValue);
				//$sNodeValue = str_replace("\xc2\xa7", '§', $sNodeValue);
				//$sNodeValue = str_replace("\xa7 ", '§', $sNodeValue);
				$sNodeValue = trim( $sNodeValue, '·');
				$sWikiText .= $sNodeValue;
				continue;
			}

			$sStartingTag = '';
			$sClosingTag = '';

			switch( strtolower( $oChild->nodeName ) ) {
				case 'b':
				case 'strong':
					$sStartingTag = "''";
					$sClosingTag = "''";
					break;
				case 'i':
				case 'em':
					$sStartingTag = "'''";
					$sClosingTag = "'''";
					break;
				case 'u':
					$sStartingTag = '<u>';
					$sClosingTag = '</u>';
					break;
				case 'img':
					$sSrc = $oChild->getAttribute( 'src' );
					$iWidth  = $oChild->getAttribute( 'width' );
					$iHeight = $oChild->getAttribute( 'height' );
					if( !empty($iHeight) ) $iHeight = 'x'.$iHeight;
					$sDimensions = '';
					if($iWidth) {
						$sDimensions = '|'.$iWidth.$iHeight.'px';
					}
					$this->output( 'Found "'.$sSrc.'"' );
					echo "\n".realpath($sSrc)."\n";
					$sImageName = basename( $sSrc );
					$sImageName = $this->sCurrentTargetArticleName.'_'.$sImageName;
					$sImageName = preg_replace('/[^(\x20-\x7F)]*/','', $sImageName); //Remove all non-ascii chars; HINT: http://www.stemkoski.com/php-remove-non-ascii-characters-from-a-string/
					// just for the moment... should be implemented completely different
					if( !empty( $this->sSourceFolderRealPath ) ) {
						$this->aFiles[$this->sSourceFolderRealPath.'/'.$sSrc] = $sImageName;
					} else {
						$this->aFiles[$sSrc] = $sImageName;
					}
					$this->iImageCount++;

					$oFileTitle = Title::makeTitle(NS_FILE, $sImageName);
					$this->output( $oFileTitle->getPrefixedText() );
					$sStartingTag = '[['.$oFileTitle->getPrefixedText().$sDimensions;
					$sClosingTag = ']]';
					break;
				case 'span':
					$sClass = $oChild->getAttribute('class');
					if( $sClass == 'UnterstrichenZchn') {
						$sStartingTag = '<u>';
						$sClosingTag = '</u>';
					}
					break;
			}
			
			$sWikiText .= $sStartingTag;
			
			if( $oChild->hasChildNodes() ) {
				$this->processInline($oChild, $sWikiText);
			}
			
			$sWikiText .= $sClosingTag;
		}
	}
	
	/**
	 * 
	 * @param DOMElement $oWSElement
	 * @param string $sWikiText
	 */
	public function processTable( $oWSElement, &$sWikiText ) {
		$this->output('Processing table');
		wfRunHooks( 'BSMigrationHelperBeforeProcessingTable', array( $this, $oWSElement, &$sWikiText) );
		$sWikiText .= "\n\n".'{| class="wikitable"'."\n";
		foreach( $oWSElement->childNodes as $oTableRow ) {
			$this->processTableRow($oTableRow, $sWikiText);
		}
		$sWikiText .= "\n".'|}'."\n\n";
		wfRunHooks( 'BSMigrationHelperAfterProcessingTable', array( $this, $oWSElement, &$sWikiText) );
	}
	
	/**
	 * 
	 * @param DOMElement $oWSElement
	 * @param string $sWikiText
	 */
	public function processTableRow($oTableRow, &$sWikiText) {
		if( $oTableRow->nodeValue == 'tbody' ) {
			foreach( $oTableRow->childNodes as $oChild ) {
				$this->processTableRow($oChild, $sWikiText);
			}
			return;
		}
		
		if( $oTableRow->nodeName != 'tr' ) return;
		$sWikiText .= "\n".'|-'."\n";
//		$sWikiText .= '|-';
		
		foreach( $oTableRow->childNodes as $oChild ) { //td
			$this->processTableData($oChild, $sWikiText);
		}
	}
	
	/**
	 * 
	 * @param DOMElement $oChild "TD"
	 * @param string $sWikiText
	 */
	public function processTableData($oChild, &$sWikiText){
		if( $oChild instanceof DOMElement == false ) return;
		if( $oChild->nodeName != 'td' ) return false;
//		$sWikiText .= "\n".'|'."\n";
		$sWikiText .= "\n";
		if( $oChild->hasAttributes() ) {
			if( $oChild->getAttribute( 'colspan')  ) {
				$iColspan = (int) $oChild->getAttribute( 'colspan');
				$sWikiText .= '| colspan="'.$iColspan.'"';
			}
		}
		$sWikiText .= '|'."\n";

		foreach( $oChild->childNodes as $oCellData ) {
			$this->processWordSectionElement($oCellData, $sWikiText);
			$sWikiText = trim($sWikiText, "\n");
		}
	}
	
	/**
	 * !!!!!!!unused so far!!!!!!!!!!!!!
	 * @param type $pageName
	 * @param type $sText
	 */
	private function addPageToResultWikiText( $pageName, $sText ) {
		$this->output( 'Creating article "'.$pageName.'"...'."\n" );

		file_put_contents( 
			$this->sTarget.'/'.
			str_replace(
				array(' ', ':'), 
				'_', utf8_decode($pageName)
			).'.wiki', 
			$sText
		);
	}
	
	/**
	 * adds content to the import xml file
	 * @param array $aArtcles | array( 'Articlename' => 'Wikitext' );
	 * @param string $sFilename | i. e. /var/tmp/myimport.xml
	 */
	public function addPageToResultXML( $sPageName, $sText ) {
		if( !empty( $this->sBookTitle ) ) {
			$sText = $this->prependString( '<bs:bookshelf src="'.$this->sBookTitle.'" />'."\n", $sText );
		}

		$resultPageElement = $this->oResultDocument->createElement('page');
		$resultPageTitleElement = $this->oResultDocument->createElement('title');
		// http://stackoverflow.com/questions/17027043/unterminated-entity-reference-php
		// http://www.zfforum.de/web-webservices/8593-unterminated-entity-reference-xml-zeichen-wird-nicht-gespeichert.html
		$resultPageTitleElement->nodeValue = htmlspecialchars( $sPageName ); // Problem here ;(
		$resultPageElement->appendChild( $resultPageTitleElement );
		$resultPageRevisonElement = $this->oResultDocument->createElement('revision');
		$resultPageRevisonTextElement = $this->oResultDocument->createElement('text');
		$resultPageRevisonTextElement->nodeValue = htmlspecialchars( $sText, ENT_IGNORE );
		//$resultPageContentCDATA = $resultDoc->createCDATASection( $pageContent );
		$resultPageRevisonTextElement->setAttribute( 'xml:space', 'preserve' );
		//$resultPageRevisonTextElement->appendChild($resultPageContentCDATA);
		$resultPageRevisonElement->appendChild($resultPageRevisonTextElement);
		$resultPageElement->appendChild( $resultPageRevisonElement );
		$this->oResultDocument->documentElement->appendChild($resultPageElement);
		$this->numberOfArticlesCreated++;
		
	}
	
	/**
	 * save the given data to a xml file
	 * @param string $sPath | Path to the xml file you want to create
	 */
	public function saveDocument( $sPath  = null ) {
		if( $sPath === null ) {
			$sPath = $this->sSavePath;
		}
		$this->oResultDocument->save( $sPath.'/result.xml' );
	}
	
	public function output( $sOutput ) {
		echo "\n".$sOutput;
	}
	
	/**
	 * if you want to create a bookshelf tag automatically on every article
	 * you need to set a booktitle
	 * @param string $sBookTitle
	 */
	public function setBookTitle( $sBookTitle ) {
		$this->sBookTitle = $sBookTitle;
	}
	
	/**
	 * this mthod just prepends a given string to another string
	 * @param string $sPrependText
	 * @param string $sText
	 * @return string
	 */
	public function prependString( $sPrependText, $sText ) {
		return $sPrependText.$sText;
	}
	
	/**
	 * returns an array of all files that are processed
	 * i. e. array( 'filesource' => 'mediawiki filename' )
	 * @return array
	 */
	public function getAllFiles() {
		return $this->aFiles;
	}
	
	/**
	 * If you want to extract images of this document you should set the source folder where the
	 * image is referenced i. e. /foo/bar/mysite.htm contains a link to images/myimage.jpg
	 * you should set $sPath to dirname('/foo/bar/mysite.htm')
	 * @param string $sPath
	 */
	public function setSourceFolderRealPath( $sPath ) {
		$this->sSourceFolderRealPath = $sPath;
	}
	
	public function setSavePath( $sSavePath ) {
		$this->sSavePath = $sSavePath;
	}
	
	public function saveDocumentFiles( $sPath = null ) {
		if( $sPath === null ) {
			$sPath = $this->sSavePath.'/images';
		}
		//return;
		if( !file_exists( $sPath ) ) {
			mkdir( $sPath );
		}
		foreach( $this->aFiles as $sFilePath => $sWikiFilename ) {
			// avoid double urlencode problems %2520 = urlencoded( '%20' )
			$sFilePath = str_replace( '%2520', '%20', $sFilePath );
			$sFilePath = urldecode( $sFilePath );
			$result = copy( $sFilePath, $sPath.'/'.$sWikiFilename );
			if( $result === true ) {
				$this->aFilesCreated[] = $sWikiFilename;
				echo "\nFile $sWikiFilename was created.";
			} else {
				$this->aFilesNotCreated[] = $sWikiFilename;
				echo "\nError creating file $sWikiFilename.";
			}
		}
	}
	
	public function printFileCreateErrors() {
		if( count( $this->aFilesNotCreated ) ) {
			echo "\n\nFollowing files/images had issues creating:\n";
		} else {
			echo "\n\nNo errors creating images\n";
		}
		foreach( $this->aFilesNotCreated as $sFilename ) {
			echo "\n* ".$sFilename;
		}
	}
	
	public function printFileCreateSuccess() {
		if( count( $this->aFilesCreated ) ) {
			echo "\n\nFollowing files/images were created";
		}
		foreach( $this->aFilesCreated as $sFilename ) {
			echo "\n* ".$sFilename;
		}
	}
	
	public function setCurrentTargetArticleName( $sArticleName ) {
		$this->sCurrentTargetArticleName = $sArticleName;
	}
	
	public function setMainArticleName( $sArticleName ) {
		$this->sMainArticleName = $sArticleName;
	}

}
