<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $

 */
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
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
