<?php
/**
 * Description of HTMLFormEx
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLFormEx extends HTMLForm {

	function displaySection( $fields, $sectionName = '', $fieldsetIDPrefix = '', &$hasUserVisibleFields = false ) {
		$tableHtml = '';
		$subsectionHtml = '';
		$hasLeftColumn = false;
		$map = array();
		foreach( $fields as $key => $value ) {
			$sKey = $this->mMessagePrefix . '-' . strtolower( $key );
			$map[$key] = wfMessage( $sKey )->plain();
		}

		asort($map);

		foreach( $map as $key => $legend ) {
			$value = $fields[$key];
			if ( is_object( $value ) ) {
				$v = empty( $value->mParams['nodata'] )
					? $this->mFieldData[$key]
					: $value->getDefault();
				$tableHtml .= $value->getTableRow( $v );

				if( $value->getLabel() != '&nbsp;' )
					$hasLeftColumn = true;
			} elseif ( is_array( $value ) ) {
				$section = $this->displaySection( $value, $key );
				$subsectionHtml .= self::fieldset( $legend, $section, array( 'class' => 'bs-prefs', 'id' => $sectionName.$key ) ) . "\n";
			}
		}

		$classes = array();
		if( !$hasLeftColumn ) // Avoid strange spacing when no labels exist
			$classes[] = 'mw-htmlform-nolabel';
		$attribs = array(
			'class' => implode( ' ', $classes ),
		);
		if ( $sectionName )
			$attribs['id'] = Sanitizer::escapeId( "mw-htmlform-$sectionName" );

		$tableHtml = Html::rawElement( 'table', $attribs,
			Html::rawElement( 'tbody', array(), "\n$tableHtml\n" ) ) . "\n";

		return $subsectionHtml . "\n" . $tableHtml;
	}

	public static function fieldset( $legend = false, $content = false, $attribs = array() ) {
		$s = Xml::openElement( 'fieldset', $attribs ) . "\n";
		if ( $legend ) {
			$s .= Xml::element( 'legend', array('class' => 'bs-prefs-head'), $legend." " ) . "\n";
		}
		if ( $content !== false ) {
			$s .= Xml::openElement( 'div', array('class' => 'bs-prefs-body') );
			$s .= $content . "\n";
			$s .= Xml::closeElement( 'div' );
			$s .= Xml::closeElement( 'fieldset' ) . "\n";
		}

		return $s;
	}

}

//HTMLForm::$typeMappings['check'] = 'HTMLCheckFieldOverride';
//HTMLForm::$typeMappings['toggle'] = 'HTMLCheckFieldOverride';
