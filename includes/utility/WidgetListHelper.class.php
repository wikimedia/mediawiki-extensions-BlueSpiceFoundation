<?php
/**
 * A helper class for processing user generated widget settings
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
// Last review: MRG (01.07.11 02:15)
class BsWidgetListHelper {
	/**
	 * The static array of instances for the n-glton pattern
	 * @var array
	 */
	static protected $aInstances = array();

	/**
	 * Corresponding MediaWiki Title object
	 * @var Title
	 */
	protected $oTitle         = null;

	/**
	 * Array of ViewWidget objects
	 * @var array
	 */
	protected $aWidgetList    = array();

	/**
	 * All registred keywords with callback functions. array( 'MYKEYWORD' => $callable )
	 * @var array
	 */
	protected $aKeywords      = array();

	/**
	 * Caching flag to prevent multiple parsings
	 * @var bool
	 */
	protected $bAlreadyParsed = false;

	/**
	 * The constructor of the BsWidgetListHelper class
	 * @param Title $oTitle
	 */
	private function __construct( $oTitle ) {
		$this->oTitle = $oTitle;
		$aKeywords = array();
		wfRunHooks( 'BSWidgetListHelperInitKeyWords' , array( &$aKeywords, $oTitle ) );
		$this->aKeywords = $aKeywords;
	}

	/**
	 * N-glton pattern implementation to prevent multiple parsing.
	 * @param Title $oTitle
	 * @return BsWidgetListHelper
	 */
	public static function getInstanceForTitle( $oTitle ){
		$sTitleKey = $oTitle->getPrefixedDBkey();
		if( !isset( self::$aInstances[$sTitleKey] ) || self::$aInstances[$sTitleKey] === null ) {
			self::$aInstances[$sTitleKey] = new BsWidgetListHelper( $oTitle );
		}
		return self::$aInstances[$sTitleKey];
	}

	/**
	 * Returns a list of ViewWidget objects parsed from the internal set Title object.
	 * @return array of ViewWidget objects
	 */
	public function getWidgets( $aParams = array() ) {
		$aParams = array_merge(
			array(
				'force-parsing' => false
				),
			$aParams
		);

		if( $aParams['force-parsing'] === false && $this->bAlreadyParsed ){
			return $this->aWidgetList;
		}

		$sKey = BsCacheHelper::getCacheKey( 'BlueSpice', 'WidgetListHelper', $this->oTitle->getPrefixedDBkey() );
		$aData = BsCacheHelper::get( $sKey );

		if( $aData !== false ) {
			wfDebugLog( 'BsMemcached', __CLASS__.': Fetching WidgetList page content from cache' );
			$sArticleContent = $aData;
		} else {
			wfDebugLog( 'BsMemcached', __CLASS__.': Fetching WidgetList page content from DB' );
			$oWikiPage = WikiPage::factory( $this->oTitle );
			$sArticleContent = $oWikiPage->getContent()->getNativeData();
			$sArticleContent    = preg_replace( '#<noinclude>.*?<\/noinclude>#si', '', $sArticleContent );

			BsCacheHelper::set( $sKey, $sArticleContent );
		}

		if( empty ( $sArticleContent ) ) {
			$this->aWidgetList = array();
			return $this->aWidgetList;
		}
		$aLines             = explode( "\n", $sArticleContent );
		$oCurrentWidgetView = null;
		$oCustomListView    = new ViewBaseElement();
		$oCustomListView->setTemplate('*[[{TITLE}|{DESCRIPTION}]]' . "\n");
		$oCustomExternalLinkListView = new ViewBaseElement();
		$oCustomExternalLinkListView->setTemplate( '*[{TITLE} {DESCRIPTION}]' . "\n");

		$oCore = BsCore::getInstance();

		foreach( $aLines as $sLine ){
			$iDepth = 0;
			$bIsIndentCharacter = true;
			do {
				if ( isset( $sLine[$iDepth] ) && $sLine[$iDepth] == '*' ) $iDepth++;
				else $bIsIndentCharacter = false;
			}
			while ($bIsIndentCharacter);
			$sLine = trim(substr($sLine, $iDepth));
			if (empty($sLine))
				continue;

			$sPotentialKeyword = strtoupper( $sLine );
			if ( isset( $this->aKeywords[$sPotentialKeyword] )
				&& is_callable( $this->aKeywords[$sPotentialKeyword] ) ) {

				if ( $oCurrentWidgetView !== null ) {
					$sPageLinkListResults = $oCustomListView->execute();
					$aExternalLinkListResults = $oCustomExternalLinkListView->execute();
					$this->aWidgetList[] = $oCurrentWidgetView->setBody(
							$oCore->parseWikiText( $sPageLinkListResults . $aExternalLinkListResults, RequestContext::getMain()->getTitle() )
					);
					$oCurrentWidgetView = null;
				}
				$oWidget = call_user_func_array( $this->aKeywords[$sPotentialKeyword], array() );
				if( $oWidget !== null ) $this->aWidgetList[] = $oWidget;
				continue;
			}

			if ( $iDepth == 1 ) {
				if ($oCurrentWidgetView !== null) {
					$sPageLinkListResults = $oCustomListView->execute();
					$aExternalLinkListResults = $oCustomExternalLinkListView->execute();
					$this->aWidgetList[] = $oCurrentWidgetView->setBody(
							$oCore->parseWikiText( $sPageLinkListResults . $aExternalLinkListResults, RequestContext::getMain()->getTitle() )
					);
					$oCurrentWidgetView = null;
				}
				$oCurrentWidgetView = new ViewWidget();
				$oCurrentWidgetView->setTitle( $sLine )
					->setAdditionalBodyClasses( array( 'bs-nav-links' ) );
				$oCustomListView = new ViewBaseElement();
				$oCustomListView->setTemplate( '*[[{TITLE}|{DESCRIPTION}]]' . "\n" );
				$oCustomExternalLinkListView = new ViewBaseElement();
				$oCustomExternalLinkListView->setTemplate( '*[{TITLE} {DESCRIPTION}]' . "\n");
			}
			if ($iDepth == 2) {
				$sLine = $this->cleanWikiLink( $sLine );

				$sTitle = $sLine;
				$sDescription = $sLine;

				$aParts = explode('|', $sLine, 2);
				if (count($aParts) > 1) {
					$sTitle = $aParts[0];
					$sDescription = $aParts[1];
				}

				$oTitle = Title::newFromText( $sTitle );
				if (is_object($oTitle)) { // CR RBV (06.06.11 15:25): Why not $oTitle === null?
					$oCustomListView->addData(array(
						'TITLE' => $oTitle->getPrefixedText(),
						'DESCRIPTION' => BsStringHelper::shorten( $sDescription, array( 'max-length' => 25, 'position' => 'middle' ) )
						)
					);
					continue;
				}

				//Handle External Links
				if ( preg_match( '#^\[http.*?\]$#si', $sLine ) ) {
					$sLine = substr( $sLine, 1, -1 );

					$sLink = $sLine;
					$sDescription = $sLine;

					$aExtLinkParts = explode(' ', $sLine, 2);
					if( count( $aExtLinkParts ) > 1 ) {
						$sLink = $aExtLinkParts[0];
						$sDescription = $aExtLinkParts[1];
					}
					$oCustomExternalLinkListView->addData(array(
						'TITLE' => $sLink,
						'DESCRIPTION' => BsStringHelper::shorten( $sDescription, array( 'max-length' => 25, 'position' => 'middle' ) )
					));
				}
			}
		}
		if ($oCurrentWidgetView !== null) { // TODO RBV (23.02.11 14:45): This is just a workaround. At given time: review logic!
			$sPageLinkListResults = $oCustomListView->execute();
			$aExternalLinkListResults = $oCustomExternalLinkListView->execute();
			$this->aWidgetList[] = $oCurrentWidgetView->setBody(
					$oCore->parseWikiText( $sPageLinkListResults . $aExternalLinkListResults, RequestContext::getMain()->getTitle() )
			);
		}

		return $this->aWidgetList;
	}

	/**
	 * Saves a Wiget list in JSON Format to the internal title object. NOT YET IMPLEMENTED! WILL THROW EXCEPTION!
	 * @param string $sJSON
	 * @param Title $oTitle
	 * @return boolen true on success, false on error.
	 */
	public function saveJSONWidgetList( $sJSON, $oTitle ) {
		throw new BsException( 'Not yet implemented' );
	}

	/**
	 * Removes '[[' and ']]' from a wikilink line
	 * @param string $sString
	 * @return string cleand string
	 */
	private function cleanWikiLink( $sString ) {
		if ( preg_match( '#^\[\[.*?\]\]$#si', $sString ) ) {
			return substr( $sString, 2, -2 );
		}
		return $sString;
	}
}
