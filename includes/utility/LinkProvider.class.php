<?php
// Last review MRG (21.09.10 13:08)
/**
 * This class provides functions to generate multiple types of links (WikiText or HTML anchors) from various sources.
 * Hint: http://www.mediawiki.org/wiki/Help:Links
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsLinkProvider {
	/**
	 * Creates an HTML anchor tag (<a>) for the UserPage of the given User object. Allows additional configuration of this tag.
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
		$sUserDisplayName = BsUserHelper::getUserDisplayName( $oUser );
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
	 * Creates a WikiText link to the provided users UserPage.
	 * @param User $oUser The MediaWiki User object a wikilink should be created for.
	 * @param String $sLabel If the link should have a special text instead of the users name, this parameter can contain it. By default the users real name (if set) or canonical username are used. I.e. $sLabel = 'Elessars life'
	 * @param String $sJumpMark You can add a jumpmark to the link to go to a specific section of the page. I.e. $sJumpMark = 'vita'
	 * @param Boolean $bUseCanonicalAsLabel Sometimes it's preferred to use the canonical username as lable instead of the real name. Use this flag for it.
	 * @return String Something like [[Aragorn[#vita]|Elessars life]]
	 */
	public static function makeWikiLinkForUser( User $oUser, $sLabel = '', $sJumpMark = '', $bUseCanonicalAsLabel = false ) {
		$sWikiLink = '';

		// TODO MRG (21.09.10 12:50): implement...
		throw new BsException('Not yet implemented');

		return $sWikiLink;
	}

	//TODO: Add "$aAction" and "$arAdditionalQueryStringParameters" to the parameter list
	/**
	 * Creates an HTML anchor tag (<a>) for the given Title object. Allows additional configuration of this tag.
	 * @param Title $oTitle The MediaWiki Title object a anchor tag should be created for.
	 * @param String $sLabel If the link should have a special text instead of the articles title, this parameter can contain it. I.e. $sLabel = 'Home of Bilbo Baggins'
	 * @param String $sTitle This value will be used for the anchor tags "title" attribute. If nothing is provided the $sLable text will be used. I.e. $sTitle = 'Bagend'
	 * @param Boolean $bRelative Switch to make urls relative or absolute.
	 * @param Array $aAdditionalQueryString If - for some reason - additional querystring parameters are needed, those can be provided as an associative array. I.e. $arAdditionalQueryString = array( 'action' => 'edit', 'knock' => 'three times') will result in something like 'href ="index.php?title=Bagend$action=edit&knock=tree%20times"' )
	 * @param Array $aClasses Simple array of classes to be used within the "class" arrtibute of the anchor tag. I.e. $arClasses = array( 'green-underline', 'bold-text' ) will result in 'class="green-underline bold-text"'
	 * @param Array $aStyles An associative array of css style statements. I.e. $arStyles = array( 'background-color' => '#00FF00', 'margin-left' => '10px' ) will result in 'style="background-color: #00FF00; margin-left: 10px;"'
	 * @param Array $aAdditionalAttributes If special attributes are required, you can pass them in an associative array. Attention: attibutes that can be set by parameter (style, class, title, ...) are not allowed! I.e. $arAdditionalAttributes = array( 'target' => '_blank', 'name' => 'Bagend', 'title' => 'Not in the Shire' ) will result in <a href="[...]" title="Gandalf" [...] target="_blank" name="Bagend">[...]</a>
	 * @return String Something like '<a href="[...]index.php?title=Bagend$action=edit" title="Home of Bilbo Baggins" [...]>Home of Bilbo Baggins</a>'
	 */
	public static function makeAnchorTagForTitle( Title $oTitle, $sLabel = '', $sTitle = '', $bRelative = true, $aAdditionalQueryString = array(), $aClasses = array(), $aStyles = array(), $aAdditionalAttributes = array() ) {
		$sAnchorTag = '';

		// TODO MRG (21.09.10 12:50): implement...
		throw new BsException('Not yet implemented');

		return $sAnchorTag;
	}

	/**
	 * Creates a WikiText link to the provided Title. This is a wrapper function for makeWikiLinkForTitleString.
	 * @param Title $oTitle The MediaWiki Title object a wikilink should be created for.
	 * @param String $sLabel If the link should have a alternative name instead of the title name, this parameter can contain it. I.e. $sLabel = 'Giant spider in Mordor'
	 * @param String $sJumpMark You can add a jumpmark to the link to go to a specific section of the page. I.e. $sJumpMark = 'vita'
	 * @return String Something like [[Mordor#Shelob|Giant spider in Mordor]]
	 */
	public static function makeWikiLinkForTitle( Title $oTitle, $sLabel = '', $sJumpMark = '' )
	{
		$sTitle = $oTitle->getText();
		$sNamespaceText = $oTitle->getNsText();

		$sWikiLink = BsLinkProvider::makeWikiLinkForTitleString($sTitle, $sNamespaceText, $sLabel, $sJumpMark);

		return $sWikiLink;
	}


	/**
	 * Creates a WikiText link to the provided Title.
	 * @param Title $sTitle The title string a wikilink should be created for.
	 * @param String $sNamespace The namespace text string for the wikilink title.
	 * @param String $sLabel If the link should have a alternative name instead of the title name, this parameter can contain it. I.e. $sLabel = 'Giant spider in Mordor'
	 * @param String $sJumpMark You can add a jumpmark to the link to go to a specific section of the page. I.e. $sJumpMark = 'vita'
	 * @return String Something like [[Mordor#Shelob|Giant spider in Mordor]]
	 */
	public static function makeWikiLinkForTitleString( $sTitle, $sNamespaceText = '', $sLabel = '', $sJumpMark = '' )
	{
		$sWikiLink = "[[";
	// TODO MRG (21.09.10 12:54): Es gibt noch den Fall, dass ich explizit in den Main-Space verlinken möchte,
	// so: [[:Test]]. Wichtiger noch: auf eine Kategorie-Seite verlinken (nicht eine Kategorie zuordnen) geht so:
	// [[:Kategorie:Test]]. Das sollte hier berücksichtigt werden könnnen.
		if ( trim($sNamespaceText) != '' ) {
			$sWikiLink .= "$sNamespaceText:";
		}
		$sWikiLink .= $sTitle;
		if ( trim($sJumpMark) != '' ) {
			$sWikiLink .= "#$sJumpMark";
		}
		if ( trim($sLabel) != '' ) {
			$sWikiLink .= "|$sLabel";
		}
		$sWikiLink .= "]]";

		return $sWikiLink;
	}


	/**
	 * Creates a WikiText Link in the form of &quot;[[NSP:Title|Description]]&quot;
	 * and escapes it if the Title is from NS_CATEGORY or NS_FILE to aviod
	 * parsing problems.
	 * @param Title $oTitle The MediaWiki Title object to link to.
	 * @param string $sDescription The description part of the link.
	 * @return string The WikiText link
	 */
	public static function makeEscapedWikiLinkForTitle( $oTitle, $sDescription = '' ) {
		if ( !( $oTitle instanceof Title ) ) return '';

		$sWikiLink = '[[';

		if ( in_array( $oTitle->getNamespace(), array( NS_FILE, NS_CATEGORY ) ) ) {
			$sWikiLink .= ':'; //Prevent file links from being rendered as <img .../> tags and category links as... nothing.
		}

		$sWikiLink .= $oTitle->getPrefixedText();

		if( !empty( $sDescription ) ) {
			$sWikiLink .= '|'.$sDescription;
		}

		$sWikiLink .= ']]';
		return $sWikiLink;
	}

	private static function makeAttributesFromArrays( $aClasses = array(), $aStyles = array(), $aAdditionalAttributes = array() ) {
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
	 * Provides a linker link
	 * @param Title $oTitle
	 * @param String $sHtml
	 * @param Array $aCustomAttribs
	 * @param Array $aQuery
	 * @param Array $aOptions
	 * @return String Link
	 */
	public static function makeLink( $oTitle, $sHtml = null, $aCustomAttribs = array(), $aQuery = array(), $aOptions = array() ) {
		return Linker::link( $oTitle, $sHtml, $aCustomAttribs, $aQuery, $aOptions );
	}

}