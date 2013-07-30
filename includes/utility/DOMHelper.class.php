<?php
/**
 * This class provides functions for common tasks while working with MediaWiki Article/Title objects.
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsDOMHelper {
	
	/**
	 * Finds the previous DOMElement starting from $oNode in the DOM tree
	 * @param DOMNode $oNode
	 * @param array $aElementNames
	 * @return DOMElement | null
	 */
	public static function getPreviousDOMElementSibling( $oNode, $aElementNames = array() ) {
		if( $oNode instanceof DOMNode == false ) return null;
		if( $oNode->previousSibling instanceof DOMElement ){
			if( empty( $aElementNames ) || in_array($oNode->previousSibling->nodeName, $aElementNames ) ) {
				return $oNode->previousSibling;
			}
		}
		return static::getPreviousDOMElementSibling( $oNode->previousSibling, $aElementNames );
	}
	
	/**
	 * Finds the next DOMElement starting from $oNode in the DOM tree
	 * @param DOMNode $oNode
	 * @param array $aElementNames
	 * @return DOMElement | null
	 */
	public static function getNextDOMElementSibling( $oNode, $aElementNames = array() ) {
		if( $oNode instanceof DOMNode == false ) return null;
		if( $oNode->nextSibling instanceof DOMElement ){
			if( empty( $aElementNames ) || in_array($oNode->nextSibling->nodeName, $aElementNames ) ) {
				return $oNode->nextSibling;
			}
		}
		return static::getNextDOMElementSibling( $oNode->nextSibling, $aElementNames );
	}
	
	/**
	 * Finds the previous DOMElement starting from $oNode in the DOM tree
	 * @param DOMNode $oNode
	 * @param array $aElementNames
	 * @return DOMElement | null
	 */
	public static function getParentDOMElement( $oNode, $aElementNames = array() ) {
		if( $oNode instanceof DOMNode == false ) return null;
		if( $oNode->parentNode instanceof DOMElement ){
			if( empty( $aElementNames ) || in_array($oNode->parentNode->nodeName, $aElementNames ) ) {
				return $oNode->parentNode;
			}
		}
		return static::getParentDOMElement( $oNode->parentNode, $aElementNames );
	}
	
	/**
	 * Finds the previous DOMElement starting from $oNode in the DOM tree
	 * @param DOMNode $oNode
	 * @param array $aElementNames
	 * @return DOMElement | null
	 */
	public static function getFirstDOMElementChild( $oNode, $aElementNames = array() ) {
		if( $oNode instanceof DOMNode == false ) return null;
		if( $oNode->firstChild instanceof DOMElement ){
			if( empty( $aElementNames ) || in_array($oNode->firstChild->nodeName, $aElementNames ) ) {
				return $oNode->firstChild;
			}
		}
		return static::getNextDOMElementSibling( $oNode->firstChild, $aElementNames );
	}
	
	/**
	 * Adds one or more entries to the "class" attribute of all childNodes in a 
	 * recursive manner
	 * @param DOMElement $oNode
	 * @param array $aClasses
	 * @param bool $bOverrideExisting Wether or not to override existing classes
	 */
	public static function addClassesRecursive( $oNode, $aClasses, $bOverrideExisting = false ) {
		$sNodesClasses = $oNode->getAttribute('class');
		$aNodesClasses = explode( ' ', $sNodesClasses );
		$oNode->setAttribute( 
			'class',
			implode( ' ', array_unique( array_merge( $aNodesClasses, $aClasses ) ) )
		);

		if( $oNode->hasChildNodes() == false ) return;
		foreach( $oNode->childNodes as $oChild ) {
			if( $oChild instanceof DOMElement == false ) continue;
			static::addClassesRecursive($oChild, $aClasses, $bOverrideExisting);
		}
	}
	
	/**
	 * 
	 * @param DOMNode $oNode
	 * @param DOMNode $oRefNode
	 */
	public static function insertAfter( $oNode, $oRefNode ) {
		if( $oRefNode->nextSibling instanceof DOMNode ) {
			$oRefNode->parentNode->insertBefore($oNode, $oRefNode->nextSibling);
		}
		else {
			$oRefNode->parentNode->appendChild($oNode);
		}
	}
}
