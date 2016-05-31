<?php
/**
 * Special page for Diagnostics for BlueSpice
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Marc Reymann <reymann@hallowelt.com>
 * @version    $Id$
 * @package    BlueSpice_Diagnostics
 * @subpackage Diagnostics
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

class SpecialDiagnostics extends BsSpecialPage {

	public function __construct() {
		parent::__construct( 'Diagnostics' );
	}

	public function execute( $par ) {
		parent::execute( $par );
		$oOutputPage = $this->getOutput();

		$oOutputPage->addHtml( "<b>Not implemented yet</b>" );
	}

	protected function getGroupName() {
		return 'bluespice';
	}
}
