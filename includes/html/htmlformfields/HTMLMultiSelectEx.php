<?php

/**
 * Description of HTMLMultiSelectEx
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLMultiSelectEx extends HTMLMultiSelectField {
	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );
		if( $p !== true ) return $p;

		if( !is_array( $value ) ) return false;

		return true;
	}

	public function getOOUIAttributes() {
		$attr = [];
		$attr += OOUI\Element::configFromHtmlAttributes(
			$this->getAttributes( [ 'disabled', 'tabindex' ] )
		);
		$attr['infusable'] = true;

		$attr['id'] = $this->mParams['id'];
		//Add options menu only if options hold more values than actual value
		$attr['options'] = $this->getOptionsOOUI();
		$attr['inputPosition'] = isset( $this->mParams['inputPosition'] ) ?
			$this->mParams['inputPosition'] : 'inline';
		$placeholder = wfMessage( 'bs-ooui-field-tagmultiselect-input-placeholder' )->plain();
		$attr['placeholder'] = isset( $this->mParams['placeholder'] ) ?
				$this->mParams['placeholder'] : $placeholder;
		$attr['allowDuplicates'] = isset( $this->mParams['allowDuplicates'] ) ?
			(bool) $this->mParams['allowDuplicates'] : false;

		$attr['allowArbitrary'] = false;
		if( isset( $this->mParams['allowedValues'] ) ) {
			$attr['allowedValues'] = $this->mParams['allowedValues'];
		}

		if( isset( $this->mParams['allowEditTags'] ) ) {
			$attr[ 'allowEditTags' ] = (bool) $this->mParams['allowEditTags'];
		}

		if( isset( $this->mParams['allowDisplayInvalidTags'] ) ) {
			$attr[ 'allowDisplayInvalidTags' ] = (bool) $this->mParams['allowDisplayInvalidTags'];
		}

		return $attr;
	}

	function getInputOOUI( $value ) {
		$this->mParent->getOutput()->addModules( 'oojs-ui-widgets' );

		$attr = $this->getOOUIAttributes();
		$attr['selected'] = $value;

		// If options hold just a list of alredy set values, disable it
		if( $value == $this->getOptions() ) {
			$attr['options'] = [];
		}

		if( !empty( $attr[ 'options' ] ) ) {
			//If there are actually options user can choose from, display
			//widget with dropdown menu
			$widget = new \BlueSpice\Html\OOUI\MenuTagMultiselectWidget( $attr );
		} else {
			//Display only current values and input
			$widget = new \BlueSpice\Html\OOUI\TagMultiselectWidget( $attr );
		}

		return $widget;
	}

	function getOptionsOOUI() {
		$options = $this->getOptions();

		if( empty( $options ) ) {
			return [];
		}

		$isAssoc = !isset( $options[0] );
		$oouiOptions = [];
		foreach( $options as $data => $label ) {
			$oouiOption = [
				'data' => $data,
				'label' => $label,
				'icon' => ''
			];

			if( $isAssoc == false ) {
				$oouiOption['data'] = $label;
			}
			$oouiOptions[] = $oouiOption;
		}

		return $oouiOptions;
	}

	function getInputHTML( $value ) {
		\RequestContext::getMain()->getOutput()->addModules( 'ext.bluespice.html.formfields.multiselect' );

		$aOptions = ( isset( $this->mParams['options'] ) ) ? $this->mParams['options'] : array();
		$html = $this->formatOptions( $aOptions, $value );

		return $html;
	}

	function formatOptions( $options, $value, $class="multiselectex" ) {
		$select = new XmlMultiSelect( $this->mName . '[]', $this->mID, $value );
		$select->setAttribute('size', 5);
		$select->setAttribute('multiple', 'true');
		$select->setAttribute('class', $class);

		if ( is_array( $options ) ) {
			$bIsAssoc = true;
			if ( array_values($options) === $options ) {
				$bIsAssoc = false;
			}

			foreach ( $options as $key => $value ) {
				// find a better way to identify associative array
				if ( $bIsAssoc )
					$select->addOption( $value, $key );
				else
					$select->addOption( $value, $value );
			}
		}

		return $select->getHTML();
	}

	function loadDataFromRequest( $request ) {
		# won't work with getCheck
		if ( $request->getCheck( 'wpEditToken' ) ) {
			$arr = $request->getArray( $this->mName );

			if( !$arr )
				$arr = array();

			return $arr;
		} else {
			return $this->getDefault();
		}
	}

	function getDefault() {
		if ( isset( $this->mDefault ) ) {
			return $this->mDefault;
		} else {
			return array();
		}
	}

	protected function needsLabel() {
		return true;
	}

}
