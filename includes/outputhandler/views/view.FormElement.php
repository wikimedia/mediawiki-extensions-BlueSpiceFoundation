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
 *
 */

// Last review: MRG20100816
// TODO MRG20100816: Hier wird nix ausgegeben. Ist das eine abstrakte Klasse? Wenn ja,
// als solche implementieren.
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewFormElement extends ViewBaseElement {
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mLabel = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mName = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mType = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mValue = '';

	/**
	 *
	 * @param string $label
	 * @return ViewFormElement
	 */
	public function setLabel( $label ) {
		$this->_mLabel = $label;
		return $this;
	}

	/**
	 *
	 * @param string $separator
	 * @return ViewFormElement
	 */
	public function setLabelSeparator( $separator ) {
		$this->_mLabelSeparator = $separator;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @return ViewFormElement
	 */
	public function setName( $name ) {
		$this->_mName = $name;
		return $this;
	}

	/**
	 *
	 * @param string $type
	 * @return ViewFormElement
	 */
	public function setType( $type ) {
		$this->_mType = $type;
		return $this;
	}

	/**
	 *
	 * @param mixed $value
	 * @return ViewFormElement
	 */
	public function setValue( $value ) {
		$this->_mValue = $value;
		return $this;
	}

	/**
	 *
	 * @param array|false $params
	 * @return string
	 */
	public function execute( $params = false ) {
		return '';
	}
}
