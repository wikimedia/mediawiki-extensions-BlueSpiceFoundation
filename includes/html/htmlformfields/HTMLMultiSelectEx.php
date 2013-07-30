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
		$html = $this->formatOptions( $this->mParams['options'], $value );

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
			};

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

class XmlMultiSelect extends XmlSelect {

	public function addOption( $name, $value = false ) {
		global $wgVersion;
		$value = ( $value !== false ) ? $value : $name;
		
		//PW: Version switch to ensure compatibility with MW1.17
		$this->options[] = $wgVersion < '1.18.0' 
			? Xml::option( $name, $value, ( array_search( $value, $this->default ) !== false ) ) 
			: array($name => $value);
	}
	
	public static function formatOptions( $options, $default = false ) {
		$data = '';

		if ( !is_array( $default ) ) $default = array();
		
		foreach( $options as $label => $value ) {
			if ( is_array( $value ) ) {
				$contents = self::formatOptions( $value, $default );
				$data .= Html::rawElement( 'optgroup', array( 'label' => $label ), $contents ) . "\n";
			} else {
				$data .= Xml::option( $label, $value, ( array_search( $value, $default ) !== false ) ) . "\n";
			}
		}

		return $data;
	}
	
	/**
	 * @return string
	 */
	public function getHTML() {
		global $wgVersion;
		if( $wgVersion < '1.18.0' ) {
			return Xml::tags( 'select', $this->attributes, implode( "\n", $this->options ) );
		}

		$contents = '';
		foreach ( $this->options as $options ) {
			$contents .= self::formatOptions( $options, $this->default );
		}
		return Xml::tags( 'select', $this->attributes, rtrim( $contents ) );
	}

}