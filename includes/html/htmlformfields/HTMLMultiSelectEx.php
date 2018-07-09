<?php

/**
 * Description of HTMLMultiSelectEx
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLMultiSelectEx extends HTMLFormField {

	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );
		if( $p !== true ) return $p;

		if( !is_array( $value ) ) return false;

		return true;
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
