<?php
/**
 * This class is the basetype for Exceptions within the Blue spice framework.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Sebastian Ulbricht, Robert Vogel
 *
 * $LastChangedDate: 2013-06-13 10:32:52 +0200 (Do, 13 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9719 $

 */

// Last Review: MRG20100813

// TODO MRG20100813: Wir müssen überlegen, wofür wir eigene Exceptions brauchen. Ich schlage vor, dass wir sie einsetzen,
// wenn ein nicht fataler Fehler passiert, der direkt mit blue spice zusammenhängt. Z.B. das Einbinden von Extensions, die
// nicht mit den richtigen Dependencies ausgestattet sind, oder beim Abrufen externer Quellen wie solr.
// Diese Exceptions sollten wir aufdröseln, also eine Klasse BsExtensionException, BsSolrException etc. Hier ist noch a bisserl
// Konzeption notwendig.
// MSC20101103: Das alles könnten wir doch über den Parameter $code abfangen, oder? Wir könnten dann BsException::EXTENSION=1
// und BsException::SOLR=2 ebenso wie BsException::FATALERROR=16 als bitkodierte Konstanten der Klasse definieren. Eine
// BsException würde dann wie folgt geworfen:
// throw new BsException( 'Can not connect to search server', BsException::SOLR|BsException::FATAL );
class BsException extends Exception {

	protected $sMessage = '';
	/**
	 * Extended constructor method of the BsException class.
	 * @param String $sMessage The literal message fore the exception, or a I18N key for the provided I18N repository object
	 * @param Integer $iCode Classic errorcode
	 * @param Exception|null $oPreviousException For use in a chain of try-catch blocks.
	 */
	public function __construct( $sMessage = '', $iCode = 0, Exception $oPreviousException = null ) {
		$this->sMessage = $sMessage;

		if ( $oPreviousException === null ) {
			parent::__construct( $sMessage, $iCode );
		} else {
			parent::__construct( $sMessage, $iCode, $oPreviousException ); // results in fatal error if $oPreviousException === null
		}
	}
	
	public function getLogMessage() {
		return $this->sMessage;
	}

	//TODO: Enhance.
}
