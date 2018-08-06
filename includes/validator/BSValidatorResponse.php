<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

class BsValidatorResponse {

	// TODO MRG20100816: Kurzer Kommentar, was der Zweck der Variablen ist.
	protected $mErrorcode;
	protected $mI18NInstanceName;
	protected $mI18NMessageKey;
	protected $mI18NTokens;
	protected $mI18NRenderedString = null;

	/**
	 * @param int $errorcode HAS TO BE (int) '0' if no error occurred
	 * @param string|null $sI18NInstanceName Name of BsI18N-Instance in Blue spice's mechanism for internationalization, where message translation can be found
	 * @param string|null $sI18NMessageKey key for BsI18N translation of error
	 * @param mixed|null $vI18NTokens spoken-word-params for BsI18N, can be single string or array with numeric keys
	 */
	public function __construct( $errorcode, $sI18NInstanceName = null, $sI18NMessageKey = null, $vI18NTokens = null ) {
		$this->mErrorcode = $errorcode;
		$this->mI18NInstanceName = $sI18NInstanceName;
		$this->mI18NMessageKey = $sI18NMessageKey;
		$this->mI18NTokens = $vI18NTokens;
	}

	/**
	 * @return int Is 0 if no error occurred
	 */
	public function getErrorCode() {
		return $this->mErrorcode;
	}

	/**
	 * @return string Human readable errormessage in User's language
	 */
	public function getI18N() {
		if ( is_null( $this->mI18NInstanceName ) ) {
			return false;
		}

		if ( is_null( $this->mI18NRenderedString ) ) {
			$this->mI18NRenderedString = ( is_null( $this->mI18NTokens ) )
				? wfMessage( $this->mI18NMessageKey )->text()
				// TODO MRG (08.02.11 00:08): msg wurde modifiziert, $default gibts nicht mehr. @Robert: macht Tokens von $default Gebrauch?
				: wfMessage( $this->mI18NMessageKey, $this->mI18NTokens )->text();
		}
		return $this->mI18NRenderedString;

		// @todo @sebastian : hier wird dem BsI18N-Mechanismus ENTWEDER
		//     # ein array (z.B. array('wort1', 'wort2', ... , 'wort99') oder
		//     # ein einzelner String
		// übergeben
	}

}
