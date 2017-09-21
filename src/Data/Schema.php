<?php

namespace BlueSpice\Data;

class Schema extends \ArrayObject {
	const FILTERABLE = 'filterable';
	const SORTABLE = 'sortable';
	const TYPE = 'type';

	/**
	 * @return string[]
	 */
	public function getUnsortableFields() {
		$unsortableFields = [];
		foreach( $this as $fieldName => $fieldDef ) {
			if( $this->fieldIsSortable( $fieldDef ) ) {
				continue;
			}

			$unsortableFields[] = $fieldName;
		}

		return $unsortableFields;
	}

	protected function fieldIsSortable( $fieldDef ) {
		if( !isset( $fieldDef[self::SORTABLE] ) ) {
			return false;
		}

		return $fieldDef[self::SORTABLE];
	}

}