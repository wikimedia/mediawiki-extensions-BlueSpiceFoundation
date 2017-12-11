<?php
/**
 * Special page for BsSpecialPage for MediaWiki
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.com>
 * @package    BlueSpice_Extensions
 * @subpackage BlueSpice
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

class BsSpecialPage extends SpecialPage {

	/**
	 * Constructor of BsSpecialPage class
	 */
	public function __construct( $name = '', $restriction = '', $listed = true,
		$function = false, $file = 'default', $includable = false ) {
		parent::__construct( $name, $restriction, $listed, $function, $file, $includable );
	}

	/**
	 * Actually render the page content.
	 * @param string $sParameter URL parameters to special page.
	 * @return string Rendered HTML output.
	 */
	public function execute( $sParameter ) {
		$this->setHeaders();
		$this->checkPermissions();
		$this->outputHeader();
	}

	protected function getGroupName() {
		return 'bluespice';
	}

	/**
	 * Shortcut to get main config object
	 * @return \Config
	 * @since 1.24
	 */
	public function getConfig() {
		return \MediaWiki\MediaWikiServices::getInstance()
			->getConfigFactory()->makeConfig( 'bsg' );
	}
}
