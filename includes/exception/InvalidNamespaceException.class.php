<?php

class BsInvalidNamespaceException extends BsException { // phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch

	/** @var array */
	private $mListOfInvalidNamespaces = [];
	/** @var array */
	private $mListOfValidNamespaces = [];

	/**
	 * Setter for internal list of invalid namespaces
	 * @param array $aInvalidNamespaces
	 * @throws InvalidArgumentException
	 */
	public function setListOfInvalidNamespaces( $aInvalidNamespaces ) {
		if ( is_array( $aInvalidNamespaces ) ) {
			$this->mListOfInvalidNamespaces = $aInvalidNamespaces;
		} else {
			throw new InvalidArgumentException();
		}
	}

	/**
	 * Getter for internal list of invalid namespaces
	 * @return array
	 */
	public function getListOfInvalidNamespaces() {
		return $this->mListOfInvalidNamespaces;
	}

	/**
	 * Setter for internal list of valid namespaces
	 * @param array $aValidNamespaces
	 * @throws InvalidArgumentException
	 */
	public function setListOfValidNamespaces( $aValidNamespaces ) {
		if ( is_array( $aValidNamespaces ) ) {
			$this->mListOfValidNamespaces = $aValidNamespaces;
		} else {
			throw new InvalidArgumentException();
		}
	}

	/**
	 * Getter for internal list of valid namespaces
	 * @return array
	 */
	public function getListOfValidNamespaces() {
		return $this->mListOfValidNamespaces;
	}
}
