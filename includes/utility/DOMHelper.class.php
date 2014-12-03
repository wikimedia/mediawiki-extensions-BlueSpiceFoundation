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
	 * Inserts a DOMNode after another DOMNode in the DOM tree
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

	/**
	 * HINT: http://stackoverflow.com/questions/1604471/how-can-i-find-an-element-by-css-class-with-xpath
	 * @param DOMDocument $oDOMDoc
	 * @param array $aClassNames
	 * @return array of DOMElement
	 */
	public static function getElementsByClassNames( $oDOMDoc, $aClassNames ) {
		$oDOMXPath = new DOMXPath( $oDOMDoc );
		$aClassQuery = array();
		foreach( $aClassNames as $sClassName ) {
			# //*[contains(concat(' ', normalize-space(@class), ' '), ' Test ')]
			$aClassQuery[] = "contains(concat(' ', normalize-space(@class), ' '), ' $sClassName ')";
		}
		$sQuery = '//*['.implode( ' or ', $aClassQuery ).']';
		$oElements = $oDOMXPath->query( $sQuery );

		$aElements = array();
		foreach( $oElements as $oElement ) {
			$aElements[] = $oElement;
		}

		return $aElements;
	}

	/**
	 * Returns an array of DOMElements of given tag names. The returned array
	 * is ordered according to the provided tag name array
	 * @param DOMDocument $oDOMDoc
	 * @param array $aTagnames
	 * @return array of DOMElements Empty array if no tags of the specified
	 * names were found of provided list was no array
	 */
	public static function getElementsByTagNames( $oDOMDoc, $aTagnames ) {
		$aElements = array();
		if( !is_array( $aTagnames ) ) {
			return $aElements;
		}
		foreach( $aTagnames as $sTagname ) {
			$oElements = $oDOMDoc->getElementsByTagName( $sTagname );
			foreach( $oElements as $oElement ) {
				$aElements[] = $oElement;
			}
		}
		return $aElements;
	}

	/**
	 * Tries to remove a DOMElement from the DOM tree.
	 * @param DOMElement $oEl
	 * @return boolean true on success, false if the operation could not be
	 * performed
	 */
	public static function removeElement($oEl) {
		if( !is_object( $oEl ) ) {
			return false;
		}
		$oParent = $oEl->parentNode;
		if( $oParent instanceof DOMNode === false ) {
			return false;
		}
		$oParent->removeChild( $oEl );
		return true;
	}

}
