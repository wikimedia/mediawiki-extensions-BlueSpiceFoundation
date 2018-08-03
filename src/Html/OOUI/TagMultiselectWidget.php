<?php

namespace BlueSpice\Html\OOUI;

use OOUI\Widget;
use BlueSpice\Html\OOUI\TagItemWidget;

class TagMultiselectWidget extends Widget {
	use \OOUI\IconElement;
	use \OOUI\IndicatorElement;
	use \OOUI\GroupElement;

	protected $handle;
	protected $input;
	protected $contentCnt;

	// Attributes
	protected $hasInput = false;
	protected $allowArbitrary;
	protected $inputPosition;
	protected $allowEditTags;
	protected $allowDuplicates;
	protected $allowedValues;
	protected $allowDisplayInvalidTypes;
	protected $selected;
	protected $placeholder;

	protected $allowedInputPositions = [ 'inline', 'outline', 'none' ];

	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$this->setConfigAttributes( $config );

		$this->hasInput = $this->inputPosition !== 'none';

		$this->initializeIndicatorElement( $config );
		$this->initializeIconElement( $config );
		$this->initializeGroupElement( $config );

		$this->addClasses( [ "oo-ui-tagMultiselectWidget", "oo-ui-tagMultiselectWidget-inlined" ] );

		$this->group->addClasses( [ "oo-ui-tagMultiselectWidget-group" ] );
		$this->contentCnt = new \OOUI\Tag();
		$this->contentCnt->addClasses( [  'oo-ui-tagMultiselectWidget-content' ] );
		$this->contentCnt->appendContent( $this->group );

		$this->handle = new \OOUI\Tag();
		$this->handle->addClasses( [ 'oo-ui-tagMultiselectWidget-handle' ] );
		$this->handle->appendContent( $this->indicator, $this->icon );

		if( $this->hasInput ) {
			if( isset( $config[ 'inputWidget' ] ) && $config[ 'inputWidget' ] instanceof \OOUI\Tag ) {
				$this->input = $config[ 'inputWidget' ];
			} else {
				$this->input = new \OOUI\Tag( 'input' );
				$this->input->addClasses( [ "oo-ui-tagMultiselectWidget-input" ] );
				if( isset( $config[ 'placeholder' ] ) ) {
					$this->input->setAttributes( [ 'placeholder' => $config[ 'placeholder' ] ] );
				}
			}

			$this->addClasses( [ "oo-ui-tagMultiselectWidget-inlined" ] );
			$this->contentCnt->appendContent( $this->input );
		}

		$this->handle->appendContent( $this->contentCnt );

		$this->addClasses( [ "oo-ui-tagMultiselectWidget" ] );
		$this->appendContent( $this->handle );

		if( !empty( $this->selected ) ) {
			$this->setValue( $this->selected );
		}
	}

	public function setValue( $value ) {
		if( !is_array( $value ) ) {
			$value = [ $value ];
		}

		$this->clearItems();
		foreach( $value as $val ) {
			if( is_string( $val ) ) {
				$this->addTag( $val );
			} else {
				$this->addTag( $val['data'], $val['label'] );
			}
		}
		return $this;
	}

	protected function addTag( $data, $label = '' ) {
		if( $this->isAllowedData( $data ) || $this->allowDisplayInvalidTypes ) {
			$newItemWidget = $this->createTagItemWidget( $data, $label );
			$this->addItems( [ $newItemWidget ] );
			return true;
		}
		return false;
	}

	protected function createTagItemWidget( $data, $label = '' ) {
		$label = ( $label != '' ) ? $label : $data;
		return new TagItemWidget( [ 'data' => $data, 'label' => $label ] );
	}

	protected function isAllowedData( $data ) {
		if( $this->allowArbitrary ) {
			return true;
		}

		if( !empty( $this->allowedValues ) && in_array( $data, $this->allowedValues ) ) {
			return true;
		}

		return false;
	}

	public function getAllowedValues() {
		return $this->allowedValues;
	}

	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return "OO.ui.TagMultiselectWidget";
	}

	/**
	 * Sets attributes, ready for infusion
	 *
	 * @param array $config
	 */
	protected function setConfigAttributes( $config ) {
		$this->allowArbitrary = isset( $config['allowArbitrary'] ) ? (bool) $config['allowArbitrary'] : false;
		$this->inputPosition = 'inline';
		if( isset( $config['inputPosition'] )
			&& in_array( $config['inputPosition'], $this->allowedInputPositions ) ) {
			$this->inputPosition = $config['inputPosition'];
		}
		$this->allowEditTags = isset( $config['allowEditTags'] ) ? (bool) $config['allowEditTags'] : false;
		$this->allowDuplicates = isset( $config['allowDuplicates'] ) ? (bool) $config['allowDuplicates'] : false;
		$this->allowedValues = isset( $config['allowedValues'] ) ? $config['allowedValues'] : [];
		$this->allowDisplayInvalidTags = isset( $config['allowDisplayInvalidTags'] ) ? (bool) $config['allowDisplayInvalidTags'] : false;
		$this->selected = isset( $config['selected'] ) && is_array( $config['selected'] ) ? $config['selected'] : [];
		// Setting placeholder on infuse will trigger "change" event of input,
		// which will trigger filtering, which will ultimately hide the menu,
		// making it positioning imposible. This is OOUI/browser bug.
		//$this->placeholder = isset( $config['placeholder'] ) ? $config['placeholder'] : '';

		$this->registerConfigCallback( function ( &$config ) {
			$config[ 'allowArbitrary' ] = $this->allowArbitrary;
			$config[ 'inputPosition' ] = $this->inputPosition;
			$config[ 'allowEditTags' ] = $this->allowEditTags;
			$config[ 'allowDuplicates' ] = $this->allowDuplicates;
			$config[ 'allowedValues' ] = $this->allowedValues;
			$config[ 'allowDisplayInvalidTags' ] = $this->allowDisplayInvalidTags;
			$config[ 'selected' ] = $this->selected;
			//$config[ 'placeholder' ] = $this->placeholder;
		} );
	}
}
