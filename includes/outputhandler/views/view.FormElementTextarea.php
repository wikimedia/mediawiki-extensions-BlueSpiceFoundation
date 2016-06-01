<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $

 */

// Last review: MRG20100816

class ViewFormElementTextarea extends ViewFormElementInput {
	public function __construct() {
		parent::__construct();
		$this->_mType = 'text';
	}

	public function execute($params = false) {
		$output = '';
		if($this->_mLabel != '') {
			$output .= '<label for="'.$this->_mId.'">'.$this->_mLabel.'</label>';
		}
		$output .= '<textarea id="'.$this->_mId.'" name="'.$this->_mName.'">'.$this->_mValue.'"</textarea>';
		return $output;
	}
}