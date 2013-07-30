<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $
 * $Id: ViewManager.class.php 314 2010-07-17 23:13:04Z mglaser $
 */

class ViewTagElement extends ViewBaseElement {
	public function  __construct() {
		parent::__construct();
		$this->_mAutoElement = 'p';
	}
	
	public function execute($params = false) {
		return '<'.$this->_mAutoElement.' id="'.$this->_mId.'"></'.$this->_mAutoElement.'>';
	}
}