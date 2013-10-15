<?php

/**
 * @copyright Hallo Welt! Medienwerkstatt GmbH
 * @author Robert Vogel <vogel@hallowelt.biz>
 */

/*
 */

//HINT: http://www.mediawiki.org/wiki/Manual:Writing_maintenance_scripts
require_once( 'BSMaintenance.php' );

class TestMail extends Maintenance {
	
	protected $defaultSubject = '204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden.';
	protected $defaultText = ' Quod erat demonstrandum. Seit 1975 fehlen in den meisten Testtexten die Zahlen, weswegen nach TypoGb. 204 § ab dem Jahr 2034 Zahlen in 86 der Texte zur Pflicht werden. 
		Nichteinhaltung wird mit bis zu 245 € oder 368 $ bestraft.
		приложениях, шрифтах, верстке и многоязычных компьютерных системах.
';

	public function __construct() {
		parent::__construct();

		$this->addOption('recipient', 'Valid user name or e-mail address to send the mail to', true, true);
		$this->addOption('framework', '[MW|BS] - To choose UserMailer or BsMailer for sending. Default is "MW".', false, true);
		$this->addOption('subject', 'An optional subject', false, true);
		$this->addOption('text', 'An optional text', false, true);
		$this->addOption('testmode', '[true|false] - Optional for framework "BS". Temporarily enables "Core::TestMode". Default is "false".', false, true);
		$this->addOption('html', '[true|false] - Optional for framework "BS". Default is "false".', false, true);
	}

	public function execute() {
		global $wgPasswordSender;
		
		$sRecipient = $this->getOption( 'recipient' );
		$sFramework = strtoupper( $this->getOption( 'framework', 'MW' ) );
		$sSubject   = $this->getOption( 'subject', $this->defaultSubject );
		$sText      = $this->getOption( 'text', $this->defaultText );
		$sTestMode  = strtolower( $this->getOption( 'testmode', 'false' ) );
		$sHTML      = strtolower( $this->getOption( 'html', 'false' ) );
		
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

		if( $sFramework == 'BS' ) {
			if( $sTestMode == 'true' ) {
				BsConfig::set( 'MW::TestMode', true ); //is not saved so it doesn't need to be reverted
			}

			$vTo = $oRecipient; //BsMailer needs User object or String, but not MailAddress object.
			if( $oRecipient->getId() == 0 ) $vTo = $oRecipientAddress->toString ();
			
			$this->output( "Using BsMailer with 'Core::TestMode' == $sTestMode and 'HTML' == $sHTML\n" );

			$bHTML = false;
			if( $sHTML == 'true' ) $bHTML = true;

			BsMailer::getInstance('MW')->setSendHTML($bHTML); //is not saved so it doesn't need to be reverted
			$oStatus = BsMailer::getInstance('MW')->send( $vTo, $sSubject, $sText );
		}
		else { 
			$this->output( "Using UserMailer\n" );
			$oStatus = UserMailer::send( $oRecipientAddress, new MailAddress( $wgPasswordSender ), $sSubject, $sText );
		}
		
		if( $oStatus->isGood() ) {
			$this->output( "Mail send\n" );
		}
		else {
			$this->output( $oStatus->getMessage() );
		}
	}
}

$maintClass = 'TestMail';
if (defined('RUN_MAINTENANCE_IF_MAIN')) {
	require_once( RUN_MAINTENANCE_IF_MAIN );
} else {
	require_once( DO_MAINTENANCE ); # Make this work on versions before 1.17
}