<?php

namespace BlueSpice;

class ListSorter {

	public const ASC = 'ASC';
	public const DESC = 'DESC';

	/**
	 *
	 * @var string
	 */
	private $direction = 'ASC';

	/**
	 *
	 * @param array $listItems
	 * @param string $direction
	 * @return array
	 */
	public function sort( $listItems, $direction = 'ASC' ) {
		$this->direction = $direction;

		usort( $listItems, function ( $itemA, $itemB ) {
			return $this->doSort( $itemA, $itemB );
		} );

		return $listItems;
	}

	private function doSort( $itemA, $itemB ) {
		$posA = 0;
		$posB = 0;

		if ( $itemA instanceof IListPositionProvider ) {
			$posA = $itemA->getPosition();
		}

		if ( $itemB instanceof IListPositionProvider ) {
			$posB = $itemB->getPosition();
		}

		if ( $this->direction === static::ASC ) {
			return $posA <=> $posB;
		}

		return $posB <=> $posA;
	}

}
