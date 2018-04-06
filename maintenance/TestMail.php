<?php

/**
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH
 * @author Robert Vogel <vogel@hallowelt.com>
 */

//HINT: https://www.mediawiki.org/wiki/Manual:Writing_maintenance_scripts
require_once( 'BSMaintenance.php' );

class TestMail extends BSMaintenance {
	
	protected $defaultSubject = '204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden.';
	protected $defaultText = ' Quod erat demonstrandum. Seit 1975 fehlen in den meisten Testtexten die Zahlen, weswegen nach TypoGb. 204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden. 
		Nichteinhaltung wird mit bis zu 245 € oder 368 $ bestraft.
		தமிழ்மொழி. テスト. የከፈተውን.
';

	public function __construct() {
		parent::__construct();

		$this->addOption('recipient', 'Valid user name or e-mail address to send the mail to', true, true);
		$this->addOption('subject', 'An optional subject', false, true);
		$this->addOption('text', 'An optional text', false, true);
	}

	public function execute() {
		global $wgPasswordSender;

		$sRecipient = $this->getOption( 'recipient' );
		$sSubject   = $this->getOption( 'subject', $this->defaultSubject );
		$sText      = $this->getOption( 'text', $this->defaultText );

		//We asume that the given recipient is a user name
		$oRecipient = User::newFromName($sRecipient);
		$oRecipientAddress = null;

		if( $oRecipient->getId() == 0 ) { //The user does not exist in DB
			//Check if it is a valid mail adress
			if( Sanitizer::validateEmail($sRecipient) ){
				$oRecipientAddress = new MailAddress( $sRecipient );
			}
		}
		else if ( $oRecipient->getEmail() ) { //not empty, false or things like this
			$oRecipientAddress = new MailAddress( $oRecipient->getEmail() );
		}

		if( $oRecipientAddress == null ) {
			$this->error( 'Not a valid user name or e-mail address or user has no e-mail address set.');
			return;
		}

		$this->output( "Sending mail to '{$oRecipientAddress->toString()}'\n" );


		$this->output( "Using UserMailer\n" );
		$oStatus = UserMailer::send( $oRecipientAddress, new MailAddress( $wgPasswordSender ), $sSubject, $sText );

		if( $oStatus->isGood() ) {
			$this->output( "Mail send\n" );
		}
		else {
			$this->output( $oStatus->getMessage() );
		}
	}
}

$maintClass = 'TestMail';
require_once RUN_MAINTENANCE_IF_MAIN;
