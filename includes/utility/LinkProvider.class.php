<?php
// Last review MRG (21.09.10 13:08)
/**
 * DEPRECATED!
 * This class provides functions to generate multiple types of links (WikiText or HTML anchors) from various sources.
 * Hint: https://www.mediawiki.org/wiki/Help:Links
 * @deprecated since version 3.0.1 - Use \BlueSpice\Services::getInstance()
 * ->getBSUtilityFactory()->getWikiTextLinksHelper and LinkRenderer service
 * instead
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsLinkProvider {
	/**
	 * DEPRECATED!
	 * Creates an HTML anchor tag (<a>) for the UserPage of the given User object. Allows additional configuration of this tag.
	 * @deprecated since version 3.0.1 - use LinkRenderer service instead
	 * @param User $oUser The MediaWiki User object a anchor tag should be created for.
	 * @param String $sLabel If the link should have a special text instead of the users name, this parameter can contain it. By default the users real name (if set) or canonical username are used. I.e. $sLabel = 'the White Wizard'
	 * @param String $sTitle This value will be used for the anchor tags "title" attribute. If nothing is provided the $sLable text will be used. I.e. $sTitle = 'Gandalf the Grey'
	 * @param Boolean $bRelative Switch to make urls relative or absolute.
	 * @param Array $aAdditionalQueryString If - for some reason - additional querystring parameters are needed, those can be provided as an associative array. I.e. $arAdditionalQueryString = array( 'action' => 'edit', 'ring' => 'Narya') will result in something like 'href ="index.php?title=User:Gandalf$action=edit&ring=Narya"' )
	 * @param Array $aClasses Simple array of classes to be used within the "class" arrtibute of the anchor tag. I.e. $arClasses = array( 'green-underline', 'bold-text' ) will result in 'class="green-underline bold-text"'
	 * @param Array $aStyles An associative array of css style statements. I.e. $arStyles = array( 'background-color' => '#00FF00', 'margin-left' => '10px' ) will result in 'style="background-color: #00FF00; margin-left: 10px;"'
	 * @param Array $aAdditionalAttributes If special attributes are required, you can pass them in an associative array. Attention: attibutes that can be set by parameter (style, class, title, ...) are not allowed! I.e. $arAdditionalAttributes = array( 'target' => '_blank', 'name' => 'Mithrandir', 'title' => 'Not a mage' ) will result in <a href="[...]" title="Gandalf" [...] target="_blank" name="Mithrandir">[...]</a>
	 * @return String Something like '<a href="[...]index.php?title=Gandalf$action=edit" title="Gandalf" [...]>the White Wizard</a>'
	 */
	public static function makeAnchorTagForUser( User $oUser, $sLabel = '', $sTitle = '', $bRelative = true, $aAdditionalQueryString = array(), $aClasses = array(), $aStyles = array(), $aAdditionalAttributes = array() ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$sUserDisplayName = \BlueSpice\Services::getInstance()
			->getBSUtilityFactory()->getUserHelper( $oUser )->getDisplayName();

		$sLabel = ( empty( $sLabel ) ) ? $sUserDisplayName : $sLabel;
		$sTitleArrtibute = ( empty( $sTitle ) ) ? 'title="'.$sLabel.'" ' : 'title="'.$sTitle.'" ';
		$sHref = $oUser->getUserPage()->getFullURL();
		if( !$oUser->getUserPage()->exists() ) {
			$aClasses[] = 'new';
			//$sHref .= '?redlink=1';
		}
		// TODO MRG (21.09.10 12:49): makeAttributesFromArrays gehört eigentlich in den TagProvider, wa?
		$sFurtherAttributes = self::makeAttributesFromArrays( $aClasses, $aStyles, $aAdditionalAttributes );
		$sAnchorTag = '<a href="%s" %s%s>%s</a>';
		$sAnchorTag = sprintf( $sAnchorTag, $sHref, $sTitleArrtibute, $sFurtherAttributes, $sLabel );

		return $sAnchorTag;
	}

	/**
	 * Creates a WikiText link to the provided Title. This is a wrapper function for makeWikiLinkForTitleString.
	 * @deprecated since version 3.0.1 - Use \BlueSpice\Services::getInstance()
	 * ->getBSUtilityFactory()->getWikiTextLinksHelper instead
	 * @param Title $oTitle The MediaWiki Title object a wikilink should be created for.
	 * @param String $sLabel If the link should have a alternative name instead of the title name, this parameter can contain it. I.e. $sLabel = 'Giant spider in Mordor'
	 * @param String $sJumpMark You can add a jumpmark to the link to go to a specific section of the page. I.e. $sJumpMark = 'vita'
	 * @return String Something like [[Mordor#Shelob|Giant spider in Mordor]]
	 */
	public static function makeWikiLinkForTitle( Title $oTitle, $sLabel = '', $sJumpMark = '' )
	{
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$util = \BlueSpice\Services::getInstance()->getBSUtilityFactory();
		$linkHelper = $util->getWikiTextLinksHelper( '' )
			->getInternalLinksHelper()->addTargets( [
			$sLabel => $oTitle
		] );
		return $linkHelper->getWikitext();
	}


	/**
	 * Creates a WikiText link to the provided Title.
	 * @deprecated since version 3.0.1 - Use \BlueSpice\Services::getInstance()
	 * ->getBSUtilityFactory()->getWikiTextLinksHelper instead
	 * @param Title $sTitle The title string a wikilink should be created for.
	 * @param String $sNamespace The namespace text string for the wikilink title.
	 * @param String $sLabel If the link should have a alternative name instead of the title name, this parameter can contain it. I.e. $sLabel = 'Giant spider in Mordor'
	 * @param String $sJumpMark You can add a jumpmark to the link to go to a specific section of the page. I.e. $sJumpMark = 'vita'
	 * @return String Something like [[Mordor#Shelob|Giant spider in Mordor]]
	 */
	public static function makeWikiLinkForTitleString( $sTitle, $sNamespaceText = '', $sLabel = '', $sJumpMark = '' )
	{
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if( !empty( $sNamespaceText ) ) {
			$sNamespaceText .= ':';
		}
		return static::makeWikiLinkForTitle(
			\Title::newFromText( $sNamespaceText . $sTitle ),
			$sLabel,
			$sJumpMark
		);
	}


	/**
	 * DEPRECATED!
	 * Creates a WikiText Link in the form of &quot;[[NSP:Title|Description]]&quot;
	 * and escapes it if the Title is from NS_CATEGORY or NS_FILE to aviod
	 * parsing problems.
	 * @deprecated since version 3.0.1 - Use \BlueSpice\Services::getInstance()
	 * ->getBSUtilityFactory()->getWikiTextLinksHelper instead
	 * @param Title $oTitle The MediaWiki Title object to link to.
	 * @param string $sDescription The description part of the link.
	 * @return string The WikiText link
	 */
	public static function makeEscapedWikiLinkForTitle( $oTitle, $sDescription = '' ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return static::makeWikiLinkForTitle( $oTitle, $sDescription );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.0.1 - not in use anymore
	 * @param array $aClasses
	 * @param array $aStyles
	 * @param array $aAdditionalAttributes
	 * @return string
	 */
	private static function makeAttributesFromArrays( $aClasses = array(), $aStyles = array(), $aAdditionalAttributes = array() ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		// TODO MRG (21.09.10 12:53): ich würde generell das Vorgehen über Option-Arrays bevorzugen. Warum sollten Klassen
		// und Styles eine Sonderstellung haben?
		$sAttributes = '';

		if ( !empty( $aClasses ) ) {
			$sAttributes .= 'class="';

			foreach( $aClasses as $sClassName ) {
				$sAttributes .= $sClassName.' ';
			}

			$sAttributes .= substr( $sAttributes, -1, 1 ).'" ';
		}

		if ( !empty( $aStyles ) )
		{
			// TODO MRG (21.09.10 12:52): implementieren
		}

		if ( !empty( $aAdditionalAttributes ) )
		{
			// TODO MRG (21.09.10 12:52): implementieren
		}

		return $sAttributes;
	}

	/**
	 * DEPRECATED!
	 * Provides a linker link
	 * @deprecated since version 3.0.1 - use LinkRenderer service instead
	 * @param Title $oTitle
	 * @param String|null $sHtml
	 * @param Array $aCustomAttribs
	 * @param Array $aQuery
	 * @param Array $aOptions
	 * @return String Link
	 */
	public static function makeLink( $oTitle, $sHtml = null, $aCustomAttribs = array(), $aQuery = array(), $aOptions = array() ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return Linker::link( $oTitle, $sHtml, $aCustomAttribs, $aQuery, $aOptions );
	}

}
