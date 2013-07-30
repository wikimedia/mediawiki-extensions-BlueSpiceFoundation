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

// Last review: MRG20100816

class ViewFormElementCheckbox extends ViewFormElementInput {
	protected $_mChecked = false;

	public function __construct() {
		parent::__construct();
		$this->_mType = 'checkbox';
	}

	public function setChecked($state) {
		$this->_mChecked = (bool)$state;
	}
	
	public function execute($params = false) {
		$output = '';
		$title = '';
		// TODO MRG20100816: Der Doppelpunkt als default ok, aber muss überschreibbar sein.
		if($this->_mLabel != '') {
			$output .= '<label for="'.$this->_mId.'">'.$this->_mLabel.':</label>'."\n";
			$title = $this->_mLabel;
		}
		// TODO MRG20100816: checked=checked (für xhtml-Kompatibilität)
		// TODO MRG20100816: Newline sollte optional sein.
		$output .= '<input id="'.$this->_mId.'" name="'.$this->_mName.'" bntype="checkbox" title="'.$title.'" type="'.$this->_mType
				.'" value="'.$this->_mValue.'"'.($this->_mChecked ? ' checked' : '').' />'."\n<br />\n";
		return $output;
	}
}