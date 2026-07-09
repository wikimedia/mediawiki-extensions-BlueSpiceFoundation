<?php

class SpecialWikiAdmin extends \BlueSpice\SpecialPage { // phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch

	public function __construct() {
		parent::__construct( 'WikiAdmin' );
	}

	public function isListed(): bool {
		return false;
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
