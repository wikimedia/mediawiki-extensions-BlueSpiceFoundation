<?php
/**
 * This class is the basetype for Exceptions within the BlueSpice framework.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Robert Vogel
 */

class BsInvalidNamespaceException extends BsException {
	private $mListOfInvalidNamespaces = array();
	private $mListOfValidNamespaces = array();

	/**
	 * Setter for internal list of invalid namespaces
	 * @param array $aInvalidNamespaces
	 * @throws InvalidArgumentException
	 */
	public function setListOfInvalidNamespaces( $aInvalidNamespaces ){
		if (is_array( $aInvalidNamespaces ) ) {
			$this->mListOfInvalidNamespaces = $aInvalidNamespaces;
		}
		else throw new InvalidArgumentException();
	}

	/**
	 * Getter for internal list of invalid namespaces
	 * @return array
	 */
	public function getListOfInvalidNamespaces(){
		return $this->mListOfInvalidNamespaces;
	}

	/**
	 * Setter for internal list of valid namespaces
	 * @param array $aValidNamespaces
	 * @throws InvalidArgumentException
	 */
	public function setListOfValidNamespaces( $aValidNamespaces ){
		if (is_array( $aValidNamespaces ) ) {
			$this->mListOfValidNamespaces = $aValidNamespaces;
		}
		else throw new InvalidArgumentException();
	}

	/**
	 * Getter for internal list of valid namespaces
	 * @return array
	 */
	public function getListOfValidNamespaces(){
		return $this->mListOfValidNamespaces;
	}
}
