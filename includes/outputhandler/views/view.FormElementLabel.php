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

// Last review MRG20100816
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewFormElementLabel extends ViewFormElement {
	protected $_mForId = '';
	protected $bUseAutoWidth = false;
	
	public function setFor($sForId) {
		$this->_mForId = $sForId;
		return $this;
	}
	
	public function setText($sText) {
		$this->_mData['text'] = $sText;
		return $this;
	}

	public function execute($params = false) {		
		$output = '<label id="'.$this->_mId.'" ';
		if($this->bUseAutoWidth) {
			$output .= 'class="label_use_auto_width" ';
		}
		if($this->_mForId) {
			$output .= 'for="'.$this->_mForId.'" ';
		}
		$output .= '>'.$this->_mData['text'].'</label>';
		return $output;
	}
	
	public function useAutoWidth($bUseAutoWidth = true) {
		$this->bUseAutoWidth = $bUseAutoWidth;
	}
}
