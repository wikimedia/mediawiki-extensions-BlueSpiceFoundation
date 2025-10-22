<?php

use MediaWiki\Context\RequestContext;
use MediaWiki\Request\WebRequest;

/**
 * Description of HTMLMultiSelectEx
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLMultiSelectEx extends HTMLMultiSelectField {
	/**
	 *
	 * @param array $value
	 * @param array $alldata
	 * @return bool
	 */
	public function validate( $value, $alldata ) {
		if ( !is_array( $value ) ) {
			return false;
		}
		$options = $this->getOptions();
		if ( array_keys( $options ) !== range( 0, count( $options ) - 1 ) ) {
			// associative array
			$options = array_keys( $options );
			return empty( array_diff( $value, $options ) );
		}
		return parent::validate( $value, $alldata );
	}

	/**
	 *
	 * @return array
	 */
	public function getOOUIAttributes() {
		$attr = [];
		$attr += OOUI\Element::configFromHtmlAttributes(
			$this->getAttributes( [ 'disabled', 'tabindex' ] )
		);
		$attr['infusable'] = true;

		$attr['id'] = $this->mParams['id'];
		// Add options menu only if options hold more values than actual value
		$attr['options'] = $this->getOptionsOOUI();
		$attr['inputPosition'] = isset( $this->mParams['inputPosition'] ) ?
			$this->mParams['inputPosition'] : 'inline';
		$placeholder = wfMessage( 'bs-ooui-field-tagmultiselect-input-placeholder' )->text();
		$attr['placeholder'] = isset( $this->mParams['placeholder'] ) ?
				$this->mParams['placeholder'] : $placeholder;
		$attr['allowDuplicates'] = isset( $this->mParams['allowDuplicates'] ) ?
			(bool)$this->mParams['allowDuplicates'] : false;
		$attr['allowArbitrary'] = false;
		if ( isset( $this->mParams['allowedValues'] ) ) {
			$attr['allowedValues'] = $this->mParams['allowedValues'];
		}

		if ( isset( $this->mParams['allowEditTags'] ) ) {
			$attr[ 'allowEditTags' ] = (bool)$this->mParams['allowEditTags'];
		}

		if ( isset( $this->mParams['allowDisplayInvalidTags'] ) ) {
			$attr[ 'allowDisplayInvalidTags' ] = (bool)$this->mParams['allowDisplayInvalidTags'];
		}

		return $attr;
	}

	/**
	 *
	 * @param array $value
	 * @return \BlueSpice\Html\OOUI\TagMultiselectWidget
	 */
	public function getInputOOUI( $value ) {
		$this->mParent->getOutput()->addModules( 'oojs-ui-widgets' );

		$attr = $this->getOOUIAttributes();
		$attr['selected'] = $this->convertValueForWidget( $value );

		// If options hold just a list of already set values, disable it
		if ( $value == $this->getOptions() ) {
			$attr['options'] = [];
		}

		// Remove selected items form options to avoid double entry's
		// See ERM24998, ERM30577
		if ( !empty( $attr['selected'] ) && !empty( $attr['options'] ) ) {
			$attr['options'] = $this->deduplicateOptions( $attr['selected'], $attr['options' ] );
		}

		if ( !empty( $attr[ 'options' ] ) ) {
			// If there are actually options user can choose from, display
			// widget with dropdown menu
			$widget = new \BlueSpice\Html\OOUI\MenuTagMultiselectWidget( $attr );
		} else {
			// Display only current values and input
			$attr['allowedValues'] = $value;
			$widget = new \BlueSpice\Html\OOUI\TagMultiselectWidget( $attr );
		}

		return $widget;
	}

	/**
	 * @param array $selected
	 * @param array $options
	 *
	 * @return array
	 */
	private function deduplicateOptions( array $selected, array $options ) {
		$deduplicated = [];
		foreach ( $options as $option ) {
			if ( !in_array( $option['data'], $selected ) ) {
				$deduplicated[] = $option;
			}
		}

		return $deduplicated;
	}

	/**
	 *
	 * @return array
	 */
	public function getOptionsOOUI() {
		$options = $this->getOptions();

		if ( empty( $options ) ) {
			return [];
		}

		$isAssoc = array_keys( $options ) !== range( 0, count( $options ) - 1 );
		$oouiOptions = [];
		foreach ( $options as $data => $label ) {
			$oouiOptions[] = [
				'data' => $isAssoc ? $data : $label,
				'label' => $label,
				'icon' => ''
			];
		}

		return $oouiOptions;
	}

	/**
	 *
	 * @param array $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		RequestContext::getMain()->getOutput()->addModules(
			'ext.bluespice.html.formfields.multiselect'
		);

		$aOptions = ( isset( $this->mParams['options'] ) ) ? $this->mParams['options'] : [];
		$html = $this->formatOptions( $aOptions, $value );

		return $html;
	}

	/**
	 *
	 * @param array $options
	 * @param array $value
	 * @param string $class
	 * @return string
	 */
	public function formatOptions( $options, $value, $class = "multiselectex" ) {
		$select = new XmlMultiSelect( $this->mName . '[]', $this->mID, $value );
		$select->setAttribute( 'size', 5 );
		$select->setAttribute( 'multiple', 'true' );
		$select->setAttribute( 'class', $class );

		if ( is_array( $options ) ) {
			$bIsAssoc = true;
			if ( array_values( $options ) === $options ) {
				$bIsAssoc = false;
			}

			foreach ( $options as $key => $value ) {
				// find a better way to identify associative array
				if ( $bIsAssoc ) {
					$select->addOption( $value, $key );
				} else {
					$select->addOption( $value, $value );
				}
			}
		}

		return $select->getHTML();
	}

	/**
	 *
	 * @param WebRequest $request
	 * @return array
	 */
	public function loadDataFromRequest( $request ) {
		# won't work with getCheck
		if ( $request->getCheck( 'wpEditToken' ) ) {
			$arr = $request->getArray( $this->mName );

			if ( !$arr ) {
				$arr = [];
			}

			return $arr;
		} else {
			return $this->getDefault();
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getDefault() {
		if ( isset( $this->mDefault ) ) {
			return $this->mDefault;
		} else {
			return [];
		}
	}

	/**
	 *
	 * @return bool
	 */
	protected function needsLabel() {
		return true;
	}

	/**
	 * @param array $value
	 * @return array
	 */
	private function convertValueForWidget( array $value ) {
		// OO.ui.MenuTagMultiselectWidget expects an array of objects with 'data' and 'label' keys
		// If option is listed in the options array, label from the option will be used, and this one
		// set here will be ignored
		$converted = [];
		foreach ( $value as $val ) {
			$converted[] = [
				'data' => $val,
				'label' => (string)$val
			];
		}
		return $converted;
	}

}
