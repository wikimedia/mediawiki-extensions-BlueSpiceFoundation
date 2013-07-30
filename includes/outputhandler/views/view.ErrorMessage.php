<?php
/**
 * Renders an error message.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Markus Glaser <glaser@hallowelt.biz>, Sebastian Ulbricht
 * @version    $Id$
 * @package    BlueSpice_Core
 * @subpackage Views
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

// Last review: RBV 2011-06-02

class ViewErrorMessage extends ViewBaseMessage {
	public function  __construct() {
		parent::__construct();
		$this->_mAutoWrap = '<div class="error">###CONTENT###</div>';
	}
}