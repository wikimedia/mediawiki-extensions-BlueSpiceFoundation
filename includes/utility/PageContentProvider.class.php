<?php
// phpcs:ignoreFile
/**
 * This class provides the rendered content of different kinds of pages, including SpecialPage, CategoryPage, ImagePage and Article.
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */

use MediaWiki\Content\ContentHandler;
use MediaWiki\Content\TextContent;
use MediaWiki\Context\DerivativeContext;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\Sanitizer;
use MediaWiki\Request\DerivativeRequest;
use MediaWiki\Request\WebRequest;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class BsPageContentProvider {
	protected $oOriginalGlobalOutputPage = null;
	protected $oOriginalGlobalParser     = null;
	protected $oOriginalGlobalTitle      = null;
	protected $oOriginalGlobalRequest    = null;

	protected $oOriginalMainRequestContextTitle = null;
	protected $oOriginalMainRequestContextOutputPage = null;
	protected $originalMainRequestContextRequest = null;

	public    $oParserOptions            = null;

	// TODO RBV (21.01.11 17:09): Better templating...
	protected $sTemplate                 = false;
	public    $bEncapsulateContent       = true;

	public static $oInstance             = null;

	/**
	 *
	 * @var RevisionLookup
	 */
	protected $revisionLookup = null;

	/**
	 * @var MediaWikiServices
	 */
	private $services = null;

	/**
	 * @var array
	 */
	private static $contents = [];

	protected function getTemplate() {
		if( $this->sTemplate !== false ) {
			return $this->sTemplate;
		}

		//Default Template
		$sTemplate = array();
		$sTemplate[] = '<div %s>';
		$sTemplate[] = '<a name="%s"></a>'; //jump-anchor
		$sTemplate[] = '<h1 class="firstHeading">%s</h1>';
		$sTemplate[] = '<div class="bodyContent">';
		$sTemplate[] = '%s';
		$sTemplate[] = '</div>';
		$sTemplate[] = '</div>';

		$this->sTemplate = implode( '', $sTemplate ); //no concat with \n,
		//because in DOM this will be a TextNode, making it more
		//difficult to traverse DOM

		return $this->sTemplate;
	}

	/**
	 * Lazy instantiation of ParserOptions
	 * @param IContextSource|null $context
	 * @return ParserOptions
	 */
	protected function getParserOptions( IContextSource $context = null ) {
		if ( !$context ) {
			$context = new DerivativeContext( RequestContext::getMain() );
		}
		$parseroptions = ParserOptions::newFromContext( $context );
		//$parseroptions->setTidy( true );
		$parseroptions->setRemoveComments( true );

		return $parseroptions;
	}

	/**
	 * Return a instance of BsPageContentProvider.
	 * @return BsPageContentProvider Instance of BsPageContentProvider
	 */
	public static function getInstance() {
		if ( self::$oInstance === null ) {
			self::$oInstance = new self();
		}

		return self::$oInstance;
	}

	/**
	 *
	 * @param RevisionLookup $revisionLookup
	 */
	public function __construct( $revisionLookup = null ) {
		$this->services = MediaWikiServices::getInstance();
		$this->revisionLookup = $revisionLookup;
		if ( $this->revisionLookup === null ) {
			$this->revisionLookup = $this->services->getRevisionLookup();
		}
	}

	/**
	 * Returns content form a given Title
	 * @param Title $oTitle Title object
	 * @param int $iAudience One of the `RevisionRecord::FOR_*` values
	 * @param User $oUser
	 * @param bool $bHTML
	 * @return String Content
	 */
	public function getContentFromTitle( Title $oTitle, $iAudience = RevisionRecord::FOR_PUBLIC, User $oUser = null, $bHTML = false ) {
		$cacheKey = md5( $oTitle->getPrefixedDBkey() . $iAudience . $bHTML . ( $oUser ? $oUser->getId() : '' ) );
		if ( isset( static::$contents[$cacheKey] ) ) {
			return static::$contents[$cacheKey];
		}
		if ( !$oTitle->exists() ) {
			static::$contents[$cacheKey] = '';
			return '';
		}
		$oRevision = $this->revisionLookup->getRevisionByTitle( $oTitle );
		if ( is_null( $oRevision ) ) {
			static::$contents[$cacheKey] = '';
			return '';
		}

		$content = $this->getContentFromRevision( $oRevision, $iAudience, $oUser, $bHTML );
		static::$contents[$cacheKey] = $content;

		return $content;
	}

	/**
	 * Returns content form a given Revision ID
	 * @param int $iRevId Revision ID
	 * @param int $iAudience One of the `RevisionRecord::FOR_*` values
	 * @param User $oUser
	 * @param bool $bHTML
	 * @return String Content
	 */
	public function getContentFromID( $iRevId, $iAudience = RevisionRecord::FOR_PUBLIC, User $oUser = null, $bHTML = false ) {
		$cacheKey = md5( $iRevId . $iAudience . $bHTML . ( $oUser ? $oUser->getId() : '' ) );
		if ( isset( static::$contents[$cacheKey] ) ) {
			return static::$contents[$cacheKey];
		}
		if ( !is_int( $iRevId ) ) {
			static::$contents[$cacheKey] = '';
			return '';
		}
		$oRevision = $this->revisionLookup->getRevisionById( $iRevId );

		$content = $this->getContentFromRevision( $oRevision, $iAudience, $oUser, $bHTML );
		static::$contents[$cacheKey] = $content;
		return $content;
	}

	/**
	 * Gets content form a given Revision object
	 * @param RevisionRecord $oRevision
	 * @param int $iAudience One of the `RevisionRecord::FOR_*` values
	 * @param User $oUser
	 * @param bool $bHTML
	 * @return String Content
	 */
	public function getContentFromRevision( $oRevision, $iAudience = RevisionRecord::FOR_PUBLIC,
			User $oUser = null, $bHTML = false ) {
		$cacheKey = md5( $oRevision->getId() . $iAudience . $bHTML . ( $oUser ? $oUser->getId() : '' ) );
		if ( isset( self::$contents[$cacheKey] ) ) {
			return self::$contents[$cacheKey];
		}

		$title = Title::newFromLinkTarget( $oRevision->getPageAsLinkTarget() );
		$contentObj = $oRevision->getContent( 'main', $iAudience, $oUser );
		if ( !( $contentObj instanceof TextContent ) ) {
			self::$contents[$cacheKey] = '';
			return '';
		}
		if ( $bHTML ) {
			$content = $this->services->getContentRenderer()
				->getParserOutput( $contentObj, $title )->getText();
		} else {
			$content = ( $contentObj instanceof TextContent ) ? $contentObj->getText() : '';
			$context = new DerivativeContext( RequestContext::getMain() );
			$parser = MediaWikiServices::getInstance()->getParserFactory()->create();
			$parser->setOptions( $this->getParserOptions( $context ) );

			$this->overrideGlobals( $title, $context );
			// FIX for #HW20130072210000028
			// Manually expand templates to allow bookshelf tags via template
			try {
				$content = $parser->preprocess(
					$content,
					$title,
					$this->getParserOptions( $context )
				);
			} catch ( Error $ex ) {
				$this->restoreGlobals();
				$content = $contentObj->getText();
			}
			$this->restoreGlobals();
		}
		static::$contents[$cacheKey] = $content;
		return $content;
	}

	/**
	 * This method returns a DOMDocument with the HTML of a Wiki page usually located in the '<div id="bodyContent">'. It supports Title objects of normal Articles, CategoryPages, ImagePages ans SpecialPages
	 * @param Title $oTitle The MediaWiki Title object from which the html output should be extracted
	 * @param Array $aParams Contains processing information, like the requested revision id (oldid) and wether to follow redirects or not.
	 * @return DOMDocument The Articles HTML output, wrapped in a '<div class="bs-page-content">' and with the title and the "bodyContent" in a DOMDocument.
	 */
	public function getDOMDocumentContentFor( $oTitle, $aParams = array() ) {
		$oDOMDoc = new DOMDocument();

		$bOldValueOfEncapsulateContent = $this->bEncapsulateContent;
		$this->bEncapsulateContent = true;

		$sHtmlContent = $this->getHTMLContentFor( $oTitle, $aParams );

		// To avoid strange errors... should never happen.
		if ( !mb_check_encoding( $sHtmlContent, 'utf8' ) ) {
			$sHtmlContent = mb_convert_encoding( $sHtmlContent, 'UTF-8', 'ISO-8859-1' );
			wfDebug(
				'BsPageContentProvider::getDOMDocumentContentFor: Content of '
				.'Title "'.$oTitle->getPrefixedText().'" was not UTF8 encoded.'
			);
		}

		$this->bEncapsulateContent = $bOldValueOfEncapsulateContent;

		libxml_use_internal_errors(true);
		$oDOMDoc->loadHTML( '<?xml encoding="utf-8" ?>' . $sHtmlContent );
		libxml_clear_errors();
		libxml_use_internal_errors(false);

		$oPreTags = $oDOMDoc->getElementsByTagName( 'pre' );
		foreach( $oPreTags as $oPreTag ) {
			if( !($oPreTag->firstChild instanceof DOMText ) ){
				continue;
			}
			//Cut off a leading linebreak in <PRE>
			if( $oPreTag->firstChild->nodeValue[0] == "\n") {
				$oPreTag->firstChild->nodeValue =
					substr( $oPreTag->firstChild->nodeValue, 1 );
			}

			if( !($oPreTag->lastChild instanceof DOMText ) ) {
				continue;
			}

			//Cut off a tailing linebreak in <PRE>
			$sLastIndex = strlen( $oPreTag->lastChild->nodeValue ) -1 ;
			$sNodeValue = $oPreTag->lastChild->nodeValue;
			if( $oPreTag->lastChild->nodeValue[$sLastIndex] == "\n" ) {
				$oPreTag->lastChild->nodeValue =
					substr( $sNodeValue, 0, $sLastIndex );
			}
		}

		return $oDOMDoc;
	}

	/**
	 * This method returns the HTML of a Wiki page usually located in the
	 * '<div id="bodyContent">'. It supports Title objects of normal Articles,
	 * CategoryPages, ImagePages and SpecialPages
	 * @param Title $oTitle The MediaWiki Title object from which the html output should be extracted
	 * @param Array $aParams Contains processing information, like the requested revision id (oldid) and wether to follow redirects or not.
	 * @return string The pages HTML output, wrapped in a '<div class="bs-page-content">' and with the title and the "bodyContent", if $this->bEncapsulateContent was not false.
	 * @global WebRequest $wgRequest
	 * @global OutputPage $wgOut
	 */
	public function getHTMLContentFor( $oTitle, $aParams = array() ){
		global $wgRequest, $wgOut;
		$aParams = array_merge(
			array(
				'oldid'            => 0,
				'follow-redirects' => false,
				'entropy'          => 0,
			),
			$aParams
		);
		$cacheKey = md5( $oTitle->getPrefixedDBkey() . FormatJson::encode( $aParams ) );
		if ( isset( self::$contents[$cacheKey] ) ) {
			return self::$contents[$cacheKey];
		}

		$oRedirectTarget = null;
		if( $oTitle->isRedirect() && $aParams['follow-redirects'] === true ){
			$oRedirectTarget = $this->getRedirectTargetRecursiveFrom( $oTitle, $aParams );
			$aParams['oldid'] = 0; //This is not the right place... at least we need a hook or something
		}

		$oTitle = ( $oRedirectTarget == null ) ? $oTitle : $oRedirectTarget;

		$context = new DerivativeContext( RequestContext::getMain() );
		$context->setRequest(
			new DerivativeRequest(
				$wgRequest,
				// $_REQUEST + i.e. oldid
				// TODO: Check if all params are necessary
				array_merge( $wgRequest->getValues(), $aParams ),
				$wgRequest->wasPosted() )
		);

		$context->setTitle( $oTitle );
		$context->setSkin( $wgOut->getSkin() );
		//Prevent "BeforePageDisplay" hook
		$context->getOutput()->setArticleBodyOnly( true ); //TODO: redundant?


		$this->overrideGlobals( $oTitle, $context );

		$actionName = Action::getActionName( $context );
		$sHTML = '';
		$sError = '';
		try {
			switch( $oTitle->getNamespace() ) {
				case NS_FILE:
					$oImagePage = ImagePage::newFromTitle( $oTitle, $context );
					// Parse to OutputPage
					$action = Action::factory( $actionName , $oImagePage, $context );
					if ( $action instanceof Action ) {
						$action->show();
					}
					else {
						$oImagePage->view();
					}
					break;

				case NS_CATEGORY:
					$oCategoryPage = CategoryPage::newFromTitle( $oTitle, $context );
					// Parse to OutputPage
					$action = Action::factory( $actionName , $oCategoryPage, $context );
					if ( $action instanceof Action ) {
						$action->show();
					}
					else {
						$oCategoryPage->view();
					}
					break;

				case NS_SPECIAL:
					/*
					 * Querystring parameters like "?from=B&namespace=6" that
					 * are needed by the special page (i.e. All Pages) have to
					 * be present in $wgRequest / the context
					 */
					$this->services->getSpecialPageFactory()->executePath( $oTitle, $context );
					$sHTML = $context->getOutput()->getHTML();
					break;

				default:
					$oArticle = Article::newFromTitle($oTitle, $context);
					$action = Action::factory( $actionName, $oArticle, $context );
					if ( $action instanceof Action ) {
						$action->show();
					}
					else {
						$oArticle->view();
					}
					break;
			}
		}
		catch( Exception $e ) {
			if( $e instanceof PermissionsError ) {
				$wgOut->showPermissionsErrorPage( $e->errors, $e->permission );
			} else if( $e instanceof ErrorPageError ) {
				$wgOut->showErrorPage( $e->title, $e->msg, $e->params );
			} else {
				$sError = $e->getMessage();
			}
			wfDebug(
				'BsPageContentProvider::getHTMLContentFor: Exception of type '
					.get_class($e)
					.' thrown on title '
					.$oTitle->getPrefixedText()
					.'. Continuing happily.'
			);
		}

		//HW#2012062710000041 — Bookshelf, PDF Export: HTTP 404 wenn nicht extistierende Artikel beinhaltet sind
		//Because in this case MW sets 404 Header in Article::view()
		if( !$oTitle->isKnown() ) {
			//Therefore we will have to reset it
			$wgRequest->response()->header( "HTTP/1.1 200 OK", true );
			wfDebug(
				'BsPageContentProvider::getHTMLContentFor: Title "'
					.$oTitle->getPrefixedText()
					.'" does not exist and caused MW to set HTTP 404 Header.'
			);
		}

		//This would be the case with normal articles and imagepages
		$sTitle = "";
		if( empty( $sHTML ) ){
			$sHTML = $wgOut->getHTML();
			$sTitle = $wgOut->getPageTitle();
		}

		$this->restoreGlobals();

		$sHTML = empty( $sHTML ) ? $context->getOutput()->getHTML() : $sHTML;


		if( !empty($sError)) {
			$sHTML .= '<div class="bs-error">'.$sError.'</div>';
		}

		$this->makeInternalAnchorNamesUnique( $sHTML, $oTitle, $aParams );

		if( $this->bEncapsulateContent ) {
			$sHTML = sprintf(
				$this->getTemplate(),
				$this->getWrapperAttributes( $oTitle ),
				'bs-ue-jumpmark-'.
				md5( $oTitle->getPrefixedText() ),
				empty( $sTitle ) ? $oTitle->getPrefixedText( ) : $sTitle,
				$sHTML
			);
		}

		$content = $this->services->getTidy()->tidy( $sHTML );
		static::$contents[$cacheKey] = $content;
		return $content;
	}

	/**
	 * @param Title $oTitle
	 * @return string
	 */
	protected function getWrapperAttributes( $oTitle ) {
		$cssClass = Sanitizer::escapeClass( 'page-'.$oTitle->getPrefixedDBKey() );
		return "class=\"bs-page-content $cssClass\"";
	}

	/**
	 * This method returns the WikiText of a Wiki page as seen in the edit view. Currently, it supports Title objects of normal Articles.
	 * @param Title $oTitle The MediaWiki Title object from which the html output should be extracted
	 * @param Array $aParams Contains processing information, like the requested revision id (oldid) and wether to follow redirects or not.
	 * @return String WikiText of the desired Article
	 */
	public function getWikiTextContentFor( Title $oTitle, $aParams = array() ) {
		$cacheKey = md5( $oTitle->getPrefixedDBkey() . FormatJson::encode( $aParams ) );
		if ( isset( self::$contents[$cacheKey] ) ) {
			return self::$contents[$cacheKey];
		}
		//TODO: Dispatch for different types [SpecialPage?, CategoryPage?, ImagePage? --> What WikiText could be received?]
		$content = $this->getWikiTextContentForArticle( $oTitle, $aParams );
		self::$contents[$cacheKey] = $content;
		return $content;
	}

	private function getWikiTextContentForArticle( Title $oTitle, $aParams = array() ) {
		$aParams = array_merge(
			array(
				'oldid'            => 0,
				'follow-redirects' => false,
				'entropy'          => 0,
				'expand-templates' => false
			),
			$aParams
		);

		$oRevision = $this->revisionLookup->getRevisionByTitle( $oTitle, $aParams['oldid'] );

		if( $oTitle->isRedirect() && $aParams['follow-redirects'] === true ){
			$oTitle = ContentHandler::makeContent(
				$this->getContentFromRevision($oRevision),
				null,
				CONTENT_MODEL_WIKITEXT
			)->getRedirectTarget();
			//TODO: This migth bypass FlaggedRevs! Test and fix if necessary!
			$oRevision = $this->revisionLookup->getRevisionByTitle( $oTitle );
		}
		if (is_null($oRevision)) {
			return '';
		}
		return $this->getContentFromRevision($oRevision);
	}

	/**
	 *
	 * @param Title $oTitle
	 * @param Array $aParams
	 * @return Title
	 */
	public function getRedirectTargetRecursiveFrom( Title $oTitle, $aParams = array() ) {
		return $this->services->getRedirectLookUp()->getRedirectTarget( $oTitle );
	}

	/**
	 *
	 * @param Title $oTitle
	 * @param Array $aParams
	 * @return Array_of_Title
	 */
	public function getRedirectChainRecursiveFrom( Title $oTitle, $aParams = array() ) {
		return [ $this->services->getRedirectLookUp()->getRedirectTarget( $oTitle ) ];
	}

	/**
	 *
	 * @param Title $oTitle
	 * @param Array $aParams
	 * @return Title
	 */
	public function getRedirectTargetFrom( Title $oTitle, $aParams = array() ) {
		return Title::newFromRedirect( $this->getWikiTextContentFor( $oTitle, $aParams ) );
	}

	//<editor-fold desc="Save, override and restore OutputPage and Parser" defaultstate="collapsed">
	/**
	 * Overrides global variables
	 *
	 * @global Parser $wgParser
	 * @global OutputPage $wgOut
	 * @global Title $wgTitle
	 * @global WebRequest $wgRequest
	 * @param Title $oTitle
	 * @param IContextSource $context
	 */
	private function overrideGlobals( $oTitle, $context = null ) {
		global $wgParser, $wgOut, $wgTitle, $wgRequest;

		//This is necessary for other extensions that may rely on $wgTitle,
		//i.e for checking permissions during rendering
		$this->oOriginalGlobalOutputPage = $wgOut;
		$this->oOriginalGlobalParser     = $wgParser;
		$this->oOriginalGlobalTitle      = $wgTitle;
		$this->oOriginalGlobalRequest    = $wgRequest;

		/*
		 * New MediaWiki RequestContext mechanism. More or less redundant but
		 * will replace "globals" in near future. As many extensions use
		 * RequestContext::getMain() instead of a passed instance of
		 * IContextSource, we need to adapt it too...
		 */
		$this->oOriginalMainRequestContextTitle
			= RequestContext::getMain()->getTitle();
		$this->oOriginalMainRequestContextOutputPage
			= RequestContext::getMain()->getOutput();
		$this->originalMainRequestContextRequest = RequestContext::getMain()->getRequest();

		$wgParser = $this->services->getParserFactory()->create();
		$wgParser->setOptions( $this->getParserOptions() );
		$wgParser->setPage( $oTitle );
		$globalParser = MediaWikiServices::getInstance()->getParser();
		$globalParser->setOptions( $wgParser->getOptions() );
		$globalParser->setPage( $wgParser->getPage() );
		if ( $globalParser->getOutput() === null ) {
			$globalParser->resetOutput();
		}

		$wgOut = new OutputPage( $context );

		if( $context ) {
			$wgRequest = $context->getRequest();
			RequestContext::getMain()->setRequest( $context->getRequest() );
		}

		$wgOut->setArticleBodyOnly( true );
		RequestContext::getMain()->setOutput( $wgOut );

		$wgTitle = $oTitle;
		RequestContext::getMain()->setTitle( $oTitle );
	}

	/**
	 * Restores the global variables
	 *
	 * @global Parser $wgParser
	 * @global OutputPage $wgOut
	 * @global Title $wgTitle
	 * @global WebRequest $wgRequest
	 */
	private function restoreGlobals() {
		global $wgParser, $wgOut, $wgTitle, $wgRequest;

		$wgOut     = $this->oOriginalGlobalOutputPage;
		$wgParser  = $this->oOriginalGlobalParser;
		$wgTitle   = $this->oOriginalGlobalTitle;
		$wgRequest = $this->oOriginalGlobalRequest;

		if ( $wgParser ) {
			$globalParser = MediaWikiServices::getInstance()->getParser();
			$globalParser->setOptions( $wgParser->getOptions() );
			$globalParser->setPage( $wgParser->getTitle() );
		}
		RequestContext::getMain()->setRequest(
			$this->originalMainRequestContextRequest
		);

		RequestContext::getMain()->setTitle(
			$this->oOriginalMainRequestContextTitle
		);
		RequestContext::getMain()->setOutput(
			$this->oOriginalMainRequestContextOutputPage
		);

		$this->oOriginalGlobalTitle= null;
	}

	/**
	 * @param string &$sHTML
	 * @param Title $oTitle
	 * @param array $aParams
	 * @return void
	 */
	private function makeInternalAnchorNamesUnique( string &$sHTML, Title $oTitle, array $aParams ) {
		$aMatches = array();
		// TODO RBV (19.07.12 09:29): Use DOM!
		// Finds all TOC links
		$tocStatus = preg_match_all( '|(<li class="toclevel-\d+.*?"><a href="#)(.*?)("><span class="tocnumber\s.*?">)|si', $sHTML, $aMatches );


		$aPatterns     = array();
		$aReplacements = array();

		$sPatternQuotedArticleTitle = preg_quote(str_replace(' ', '_', $oTitle->getPrefixedText()));
		$sPatternQuotedArticleTitleLocalURL = preg_quote( $oTitle->getLocalURL() );
		// For some reason the / in the subpage name has to be escaped
		$sPatternQuotedArticleTitleLocalURL = str_replace(
			str_replace( ' ', '_', $oTitle->getPrefixedText() ),
			str_replace( '/', '\/', $sPatternQuotedArticleTitle ),
			$sPatternQuotedArticleTitleLocalURL
		);

		if ( $tocStatus > 0 ) {
			//toc is available in $sHtml
			foreach ( $aMatches[2] as $sAnchorName ) {
				$sUniqueAnchorName = md5( $oTitle->getPrefixedText() ) . '-' .  md5( $sAnchorName );
				$sPatternQuotedAnchorName = preg_quote( $sAnchorName );
				$sUrlEncodedAnchorName = urlencode( $sAnchorName );
				$sUrlEncodedAnchorName = str_replace( '%', '.',  $sUrlEncodedAnchorName );
				$sPatternQuotedUrlAnchorName = preg_quote( $sUrlEncodedAnchorName );

				// In TOC
				$aPatterns[]     = '|<a href="#' . $sPatternQuotedAnchorName . '"><span class="tocnumber">|si';
				$aReplacements[] = '<a href="#' . $sUniqueAnchorName . '"><span class="tocnumber">';

				$this->internalAnchorPatterns(
					$aPatterns, $aReplacements, $sUniqueAnchorName, $sPatternQuotedAnchorName,
					$sPatternQuotedUrlAnchorName, $sPatternQuotedArticleTitleLocalURL
				);
			}

			$sHTML = preg_replace( $aPatterns, $aReplacements, $sHTML );
		}
		else {
			//toc is not available in $sHtml
			$headlineStatus = preg_match_all('|(<span class="mw-headline".*?id="(.*?)".*?>)|si', $sHTML, $aMatches );

			if ( $headlineStatus > 0 ) {
				foreach ( $aMatches[2] as $sAnchorName ) {
					$sUniqueAnchorName = md5( $oTitle->getPrefixedText() ) . '-' .  md5( $sAnchorName );
					$sPatternQuotedAnchorName = preg_quote( $sAnchorName );
					$sUrlEncodedAnchorName = urlencode( $sAnchorName );
					$sUrlEncodedAnchorName = str_replace( '%', '.',  $sUrlEncodedAnchorName );
					$sPatternQuotedUrlAnchorName = preg_quote( $sUrlEncodedAnchorName );

					$this->internalAnchorPatterns(
						$aPatterns, $aReplacements, $sUniqueAnchorName, $sPatternQuotedAnchorName,
						$sPatternQuotedUrlAnchorName, $sPatternQuotedArticleTitleLocalURL
					);
				}

				$sHTML = preg_replace( $aPatterns, $aReplacements, $sHTML );
			}
		}
	}

	/**
	 * @param array $pattern
	 * @param array $replacements
	 * @param string $uniqueAnchorName
	 * @param string $patternQuotedAnchorName
	 * @param string $patternQuotedUrlAnchorName
	 * @param string $patternQuotedArticleTitleLocalURL
	 * @return void
	 */
	private function internalAnchorPatterns(
		array &$pattern, array &$replacements, string $uniqueAnchorName,
		string $patternQuotedAnchorName, string $patternQuotedUrlAnchorName, string $patternQuotedArticleTitleLocalURL
	) {
		// Every single headline
		$pattern[]     = '|<a name="' . $patternQuotedAnchorName . '" id="' . $patternQuotedAnchorName . '"></a>|si';
		$replacements[] = '<a name="' . $uniqueAnchorName . '" id="' . $uniqueAnchorName . '"></a>';
		// Every single headline new
		$pattern[]     = '|<span class="mw-headline" id="' . $patternQuotedAnchorName . '"|si';
		$replacements[] = '<span class="mw-headline" id="' . $uniqueAnchorName . '"';
		// Every single jumpmark before headline
		$pattern[]     = '|<span id="' . $patternQuotedUrlAnchorName . '"|si';
		$replacements[] = '<span id="' . $uniqueAnchorName . '"';

		// In text
		$pattern[]     = '|<a href="(' . $patternQuotedArticleTitleLocalURL . ')?#' . $patternQuotedAnchorName . '"|si';
		$replacements[] = '<a href="#' . $uniqueAnchorName . '"';
		$pattern[]     = '|<a href="(' . $patternQuotedArticleTitleLocalURL . ')?#' . $patternQuotedUrlAnchorName . '"|si';
		$replacements[] = '<a href="#' . $uniqueAnchorName . '"';
	}
}
