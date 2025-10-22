<?php

use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\Parser\Sanitizer;
use MediaWiki\Xml\Xml;

/**
 * Description of HTMLFormEx
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLFormEx extends HTMLForm {

	/**
	 *
	 * @param array $fields
	 * @param string $sectionName
	 * @param string $fieldsetIDPrefix
	 * @param bool &$hasUserVisibleFields
	 * @return string
	 */
	public function displaySection( $fields, $sectionName = '', $fieldsetIDPrefix = '',
		&$hasUserVisibleFields = false ) {
		$tableHtml = '';
		$subsectionHtml = '';
		$hasLeftColumn = false;
		$map = [];
		foreach ( $fields as $key => $value ) {
			$sKey = $this->mMessagePrefix . '-' . strtolower( $key );
			$map[$key] = $this->msg( $sKey )->text();
		}

		asort( $map );

		foreach ( $map as $key => $legend ) {
			$value = $fields[$key];
			if ( is_object( $value ) ) {
				$v = empty( $value->mParams['nodata'] )
					? $this->mFieldData[$key]
					: $value->getDefault();
				$tableHtml .= $value->getTableRow( $v );

				if ( $value->getLabel() != '&nbsp;' ) {
					$hasLeftColumn = true;
				}
			} elseif ( is_array( $value ) ) {
				$section = $this->displaySection( $value, $key );
				$subsectionHtml .= self::fieldset(
					$legend,
					$section,
					[ 'class' => 'bs-prefs', 'id' => $sectionName . $key ]
				) . "\n";
			}
		}

		$classes = [];
		if ( !$hasLeftColumn ) {
			// Avoid strange spacing when no labels exist
			$classes[] = 'mw-htmlform-nolabel';
		}
		$attribs = [
			'class' => implode( ' ', $classes ),
		];
		if ( $sectionName ) {
			$attribs['id'] = Sanitizer::escapeIdForAttribute( "mw-htmlform-$sectionName" );
		}

		$tableHtml = Html::rawElement( 'table', $attribs,
			Html::rawElement( 'tbody', [], "\n$tableHtml\n" ) ) . "\n";

		return $subsectionHtml . "\n" . $tableHtml;
	}

	/**
	 *
	 * @param string|false $legend
	 * @param string|false $content
	 * @param array $attribs
	 * @return string
	 */
	public static function fieldset( $legend = false, $content = false, $attribs = [] ) {
		$s = Xml::openElement( 'fieldset', $attribs ) . "\n";
		if ( $legend ) {
			$s .= Xml::element( 'legend', [ 'class' => 'bs-prefs-head' ], $legend . " " ) . "\n";
		}
		if ( $content !== false ) {
			$s .= Xml::openElement( 'div', [ 'class' => 'bs-prefs-body' ] );
			$s .= $content . "\n";
			$s .= Xml::closeElement( 'div' );
			$s .= Xml::closeElement( 'fieldset' ) . "\n";
		}

		return $s;
	}

}

// HTMLForm::$typeMappings['check'] = 'HTMLCheckFieldOverride';
// HTMLForm::$typeMappings['toggle'] = 'HTMLCheckFieldOverride';
