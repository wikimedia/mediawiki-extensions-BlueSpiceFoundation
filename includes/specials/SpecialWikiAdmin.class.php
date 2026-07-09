<?php

class SpecialWikiAdmin extends \BlueSpice\SpecialPage {

	public function __construct() {
		parent::__construct( 'WikiAdmin', '', false );
	}

	/**
	 * @param string $par
	 */
	public function execute( $par ) {
		parent::execute( $par );
		$oOutputPage = $this->getOutput();
		$oOutputPage->setPageTitle( "WikiAdmin" );
		$oOutputPage->addHTML( '<div>WikiAdmin has been removed in BlueSpice 3</div>' );
	}

}
