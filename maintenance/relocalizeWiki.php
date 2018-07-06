<?php
require_once( 'BSMaintenance.php' );
echo "Relocalize Wiki...\n";

class RelocalizeWiki extends Maintenance {
	public $bDryRun = false;
	public $aFromNs = array();
	public $aToNs = array();
	public $iCount = 0;
	public $iPregCount = 0;
	public $bEdited = false;
	public $sOutput = '';
	public $aNothingReplaced = array();
	public $aSpecialFrom = array();
//	public $aSpecialTo = array();
	
	public function __construct() {
		parent::__construct();
		$this->addOption( 'oldLang', 'From wich Language you want to translate the Imagelink', true );
		$this->addOption( 'newLang', 'To wich Language you want to translate the Imagelink', true );
		$this->addOption( 'dry', 'Testrun without saving the article' );
		$this->requireExtension( 'BlueSpiceFoundation' );
	}
	
	public function execute() {
		$sOldLang = $this->getOption( 'oldLang', false );
		$sNewLang = $this->getOption( 'newLang', false );
		$this->bDryRun = $this->getOption( 'dry', false );
		
		$this->output( "\nLooking for namespace indexes...\n" );
		$aFromNs = $this->getNamespaceIndexes( $sOldLang );
		$this->aFromNs = $aFromNs;
		$oLang = Language::factory( $sOldLang );
		$this->aSpecialFrom = $oLang->getSpecialPageAliases();
		
		$aToNs = $this->getNamespaceIndexes( $sNewLang );
		$this->aToNs = $aToNs;

		$this->relocalizeWiki();
		
	}
	
	
	public function getNamespaceIndexes( $sLanguageCode ) {
		// workaround for bluespice namespacemanager bug
		global $wgExtraNamespaces;
		$sTempwgExtraNamespaces = $wgExtraNamespaces;
		$wgExtraNamespaces = array();
		
		$aReturn = array();

		$oLang = Language::factory( $sLanguageCode );
		
		// get index from NS_FILE e.g. File:
		$aReturn['ns'] = $oLang->getNamespaces();

		// get alias from Namespaces e.g. Image
		$aReturn['alias'] = $oLang->getNamespaceAliases();
		$aReturn['alias'] = array_flip( $aReturn['alias'] );

		$wgExtraNamespaces = $sTempwgExtraNamespaces;
		return $aReturn;
	}
	
	
	public function relocalizeWiki() {
		$oDbw = wfGetDB( DB_MASTER );
		$oRes = $oDbw->select(
				array( 'page' )
				,
				'page_id',
				'',
				__METHOD__,
				array( 'ORDER BY' => 'page_id')
		);

		$aArticleIds = array();
		while( $aData = $oRes->fetchRow() ) {
			$aArticleIds[] =  $aData['page_id'];
		}

		foreach( $aArticleIds as $iArticleId ) {
			$this->bEdited = false;
			$oArticle = Article::newFromID( $iArticleId );
			//Article::fetchContent() is deprecated.
			//Replaced by WikiPage::getContent()::getNativeData()
			$oWikiPage = WikiPage::newFromID( $iArticleId );
			$sArticleContent = ContentHandler::getContentText( $oWikiPage->getContent() );
			ob_start();
			$this->sOutput = '';
			$this->sOutput .= "\n-------- Article with ID: ".$iArticleId." --------\n";

			foreach( $this->aFromNs as $aKeys => $aValues ) {
				$iCount = 0;
				$iPregSpecialCount = 0;
				$iPregImageCount = 0;
				
				foreach( $aValues as $sNsIndex => $sNsName ) {
					if( !isset( $this->aToNs['ns'][$sNsIndex] ) ) continue;
					if( $sNsIndex === NS_TEMPLATE ) {
						$sPrefix = '{';
					} else {
						$sPrefix = '[';
					}
					$sSearchFor = $sPrefix.$sNsName.':';
					$sReplacement = $sPrefix. $this->aToNs['ns'][$sNsIndex].':';
					
					if( $sSearchFor === $sReplacement ) continue;
					
					if( $sNsIndex === NS_SPECIAL ) {
						$sArticleContent = preg_replace_callback( '#\[('.$sNsName.'\:(.*?))\]#si', array( $this, 'pregSpecialpageCallback'), $sArticleContent, -1, $iPregSpecialCount );
					} else {
						$sArticleContent = str_replace( $sSearchFor, $sReplacement, $sArticleContent, $iCount );
					}
					

					if( $iCount !== 0 ) {
						$this->sOutput .= 'Replaced "'.$sNsName.':" with "'.$this->aToNs['ns'][$sNsIndex].":\"\n";
					}
					$sArticleContent = preg_replace_callback( '#<gallery>(.*?)</gallery>#si', array( $this, 'pregImageCallback'), $sArticleContent, -1, $iPregImageCount );
					if( $iCount !== 0 || $iPregImageCount !== 0 || $iPregSpecialCount !== 0 ) {
						$this->bEdited = true;
					}
				}
			}

			if( $this->bEdited ) {
				if( !$this->bDryRun ) {
					$oArticle->doEditContent(
						ContentHandler::makeContent( $sArticleContent, $oArticle->getTitle() ),
						$sArticleContent,
						'/* Changed Localisation */',
						EDIT_FORCE_BOT | EDIT_MINOR | EDIT_SUPPRESS_RC
					);
				}
				$this->sOutput .=  "Replacement done.\n\n";
			} else {
				$this->sOutput = '';
				$this->aNothingReplaced[] = ' '.$iArticleId;
			}
			
			echo $this->sOutput;
		}
		echo "\n\nNothing replaced on Article(s) with ID(s): ".implode( ',', $this->aNothingReplaced );
	}
	
	public function pregImageCallback( &$input ) {
		$iCountNsName = 0;
		$iCountNsAlias = 0;
		$input[0] = str_replace( $this->aFromNs['ns'][NS_FILE].':', $this->aToNs['ns'][NS_FILE].':', $input[0], $iCountNsName );
		if( $iCountNsName !== 0 ) {
			for( $i = 1; $i <= $iCountNsName; $i++ ) {
				$this->sOutput .= "Replaced ".'"'.$this->aFromNs['ns'][NS_FILE].':" with "'.$this->aToNs['ns'][NS_FILE].':"'."\n";
			}
		}
		if( isset( $this->aFromNs['alias'][NS_FILE] ) ) {
			$input[0] = str_replace( $this->aFromNs['alias'][NS_FILE].':', $this->aToNs['ns'][NS_FILE].':', $input[0], $iCountNsAlias );
			if( $iCountNsAlias !== 0 ) {
				for( $i = 1; $i <= $iCountNsAlias; $i++ ) {
					$this->sOutput .= "Replaced ".'"'.$this->aFromNs['alias'][NS_FILE].':" with "'.$this->aToNs['ns'][NS_FILE].':"'."\n";
				}
			}
		}
		return $input[0];
	}
	
	public function pregSpecialpageCallback( &$input ) {
		$aReplace = explode( '|', $input[2] );
		$aReplace[0] = trim( $aReplace[0] );

		$sReplacement = '';
		foreach( $this->aSpecialFrom as $sIndexname => $sAlias ) {
			if( in_array( $aReplace[0], $sAlias ) ) {
				$sReplacement = $sIndexname;
			} elseif ( $aReplace[0] === $sIndexname ) {
				$sReplacement = $sIndexname;
			}			
		}
		
		$oSpecialPage = SpecialPage::getPage( $sReplacement );
		
		if( !is_object( $oSpecialPage ) ) return $input[0];
		
		$input[0] = str_replace( $this->aFromNs['ns'][NS_SPECIAL].':'.$aReplace[0], $this->aToNs['ns'][NS_SPECIAL].':'.$oSpecialPage->getName(), $input[0] );
		$this->sOutput .= 'Replaced "'.$this->aFromNs['ns'][NS_SPECIAL].':'.$aReplace[0].'" with "'.$this->aToNs['ns'][NS_SPECIAL].':'.$oSpecialPage->getName().'"'."\n";
		
		return $input[0];
	}
}



$maintClass = 'RelocalizeWiki';

require_once RUN_MAINTENANCE_IF_MAIN;
