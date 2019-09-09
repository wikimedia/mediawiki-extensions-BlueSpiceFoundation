<?php

/**
 * Special page for WikiAdmin of BlueSpice (MediaWiki)
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Daniel Vogel
 * @package    BlueSpiceFoundation
 * @subpackage WikiAdmin
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
class SpecialWikiAdmin extends \BlueSpice\SpecialPage {

	public function __construct() {
		parent::__construct( 'WikiAdmin', '', false );
	}

	/**
	 *
	 * @param string $par
	 */
	public function execute( $par ) {
		parent::execute( $par );
		$oOutputPage = $this->getOutput();
		$oOutputPage->setPageTitle( "WikiAdmin" );
		$oOutputPage->addHTML( '<div>WikiAdmin has been removed in BlueSpice 3</div>' );
	}

}
