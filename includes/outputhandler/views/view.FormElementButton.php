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

class ViewFormElementButton extends ViewFormElement {
	public function __construct() {
		parent::__construct();
		$this->_mType = 'submit';
	}

	// TODO MRG20100816: Wofür brauchen wir den bntype?
	// TODO MRG20100816: value=1 nur als default, das muss überschreibbar sein
	// TODO MRG20100816: Das br muss optional sein
	// TODO MRG (01.09.10 16:33): Inhalt des title-Attributs sollte nochmal überprüft werden.
	public function execute($params = false) {
		return '<button id="'.$this->_mId.'" name="'.$this->_mName.'" bntype="button" type="'.$this->_mType.'" value="'.($this->_mValue ? $this->_mValue : 1).'">'.$this->_mLabel.'</button>'."\n<br />\n";
	}
}