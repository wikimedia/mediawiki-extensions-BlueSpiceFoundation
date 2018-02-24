<?php

namespace BlueSpice\Data;

class FilterFactory {
	/**
	 *
	 * @return array
	 */
	public static function getTypeMap() {
		return [
			'string' => 'BlueSpice\Data\Filter\StringValue',
			'date'=> 'BlueSpice\Data\Filter\Date',
			#'datetime'=> 'BlueSpice\Data\Filter\DateTime',
			'boolean'=> 'BlueSpice\Data\Filter\Boolean',
			'numeric' => 'BlueSpice\Data\Filter\Numeric',
			'title' => 'BlueSpice\Data\Filter\Title',
			'templatetitle' => 'BlueSpice\Data\Filter\TemplateTitle',
			'list' => 'BlueSpice\Data\Filter\ListValue'
		];
	}

	/**
	 *
	 * @param array $filter
	 * @return \BlueSpice\Data\Filter
	 * @throws \UnexpectedValueException
	 */
	public static function newFromArray( $filter ) {
		$typeMap = static::getTypeMap();
		if( isset( $typeMap[$filter[Filter::KEY_TYPE]]) ) {
			return new $typeMap[$filter[Filter::KEY_TYPE]]( $filter );
		}
		else {
			throw new \UnexpectedValueException(
				"No filter class for '{$filter[Filter::KEY_TYPE]}' available!"
			);
		}
	}
}
