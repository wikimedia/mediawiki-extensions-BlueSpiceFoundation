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
