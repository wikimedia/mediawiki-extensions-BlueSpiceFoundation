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

// Last review: MRG20100816
// TODO MRG20100816: Hier wird nix ausgegeben. Ist das eine abstrakte Klasse? Wenn ja, als solche implementieren.

class ViewFormElement extends ViewBaseElement {
	protected $_mLabel			= '';
	protected $_mName			= '';
	protected $_mType			= '';
	protected $_mValue			= '';

	public function setLabel($label) {
		$this->_mLabel = $label;
		return $this;
	}

	public function setLabelSeparator($separator) {
		$this->_mLabelSeparator = $separator;
		return $this;
	}

	public function setName($name) {
		$this->_mName = $name;
		return $this;
	}

	public function setType($type) {
		$this->_mType = $type;
		return $this;
	}

	public function setValue($value) {
		$this->_mValue = $value;
		return $this;
	}

	public function execute($params = false) {
		return '';
	}
}