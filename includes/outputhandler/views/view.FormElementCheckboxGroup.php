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

// Last review MRG20100816


class ViewFormElementCheckboxGroup extends ViewFormElementCommonGroup {


	public function addItem($label, $value = false, $state = false) {
		$item = new ViewFormElementCheckbox();
		$item->setLabel($label);
		$item->setValue($value);
		$item->setChecked($state);
		$item->setName($this->_mName);
		parent::addItem($item);
		return $item;
	}

}