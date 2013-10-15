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

 */

class ViewFormElementSelectbox extends ViewFormElement {
	public function execute($params = false) {
		$output = '';
		if($this->_mLabel != '') {
			$output .= '<label for="'.$this->_mId.'">'.$this->_mLabel.':</label>'."\n";
			$title = $this->_mLabel;
		}
		$output .= '<select id="'.$this->_mId.'" name="'.$this->_mName.'">';
		foreach($this->_mData as $data) {
			$output .= '<option value="'.$data['value'].'">'.$data['label'].'</option>';
		}
		$output .= '</select>';
		return $output;
	}
}