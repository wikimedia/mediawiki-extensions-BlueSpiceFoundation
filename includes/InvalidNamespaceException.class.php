<?php
/**
 * This class is the basetype for Exceptions within the Blue spice framework.
 *
 * @copyright Copyright (c) 2007-2011, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Robert Vogel
 * @version 0.1.0 beta
 *
 * $LastChangedDate: 2013-06-13 09:29:57 +0200 (Do, 13 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9715 $
 * $Id: InvalidNamespaceException.class.php 9715 2013-06-13 07:29:57Z rvogel $
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