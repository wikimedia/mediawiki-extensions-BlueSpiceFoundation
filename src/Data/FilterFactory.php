<?php

namespace BlueSpice\Data;

class FilterFactory {
	/**
	 *
	 * @var array
	 */
	public static $typeMap = [
		'string' => 'BlueSpice\Data\Filter\StringValue',
		'date'=> 'BlueSpice\Data\Filter\Date',
		#'datetime'=> 'BlueSpice\Data\Filter\DateTime',
		'boolean'=> 'BlueSpice\Data\Filter\Boolean',
		'numeric' => 'BlueSpice\Data\Filter\Numeric',
		'title' => 'BlueSpice\Data\Filter\Title',
		'templatetitle' => 'BlueSpice\Data\Filter\TemplateTitle',
		'list' => 'BlueSpice\Data\Filter\ListValue'
	];

	/**
	 *
	 * @param array $filter
	 * @return \BlueSpice\Data\Filter
	 * @throws \UnexpectedValueException
	 */
	public static function newFromArray( $filter ) {
		if( isset( self::$typeMap[$filter[Filter::KEY_TYPE]]) ) {
			return new self::$typeMap[$filter[Filter::KEY_TYPE]]( $filter );
		}
		else {
			throw new \UnexpectedValueException(
				"No filter class for '{$filter[Filter::KEY_TYPE]}' available!"
			);
		}
	}
}


