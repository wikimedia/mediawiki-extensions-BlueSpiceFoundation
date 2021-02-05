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
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewBaseForm extends ViewBaseElement {
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mMethod = 'post';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mEnctype = 'multipart/form-data';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mRenderAsExt = false;
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mActionUrl = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mValidationUrl = null;

	public function __construct() {
		parent::__construct();
		$this->_mAutoElement = 'form';
	}

	/**
	 *
	 * @param bool $state
	 * @return ViewBaseForm
	 */
	public function renderAsExtFormPanel( $state = true ) {
		$this->_mRenderAsExt = $state;
		return $this;
	}

	/**
	 *
	 * @param string $url
	 * @return ViewBaseForm
	 */
	public function setActionUrl( $url ) {
		$this->_mActionUrl = $url;
		return $this;
	}

	/**
	 *
	 * @param string $method
	 * @return ViewBaseForm
	 */
	public function setMethod( $method ) {
		$this->_mMethod = $method;
		return $this;
	}

	/**
	 *
	 * @param string $enc
	 * @return ViewBaseForm
	 */
	public function setEnctype( $enc ) {
		$this->_mEnctype = $enc;
		return $this;
	}

	/**
	 *
	 * @param string $url
	 * @return ViewBaseForm
	 */
	public function setValidationUrl( $url ) {
		$this->_mValidationUrl = $url;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getAutoElementOpener() {
		$enctype = empty( $this->_mEnctype ) ? '' : ( ' enctype="' . $this->_mEnctype . '"' );
		if ( $this->_mRenderAsExt ) {
			$validation = '';
			if ( $this->_mValidationUrl ) {
				$validation = ' validate="' . $this->_mValidationUrl . '"';
			}
			// TODO MRG20100816: wozu ist das div denn da?
			return '<div id="' . $this->_mId . 'wrap"></div>'
					. '<' . $this->_mAutoElement . ' id="' . $this->_mId . '"'
					. ' renderAsExt="true"' . $validation . ' method="' . $this->_mMethod
					. '" action="' . $this->_mActionUrl . '"' . $enctype . '>';
		}
		return '<' . $this->_mAutoElement . ' id="' . $this->_mId . '" action="'
			. $this->_mActionUrl . '" method="' . $this->_mMethod . '"' . $enctype . '>';
	}

}
