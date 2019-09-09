<?php

namespace BlueSpice\Data;

abstract class SecondaryDataProvider implements ISecondaryDataProvider {

	/**
	 *
	 * @param IRecord[] $dataSets
	 * @return IRecord[]
	 */
	public function extend( $dataSets ) {
		foreach ( $dataSets as &$dataSet ) {
			$this->doExtend( $dataSet );
		}

		return $dataSets;
	}

	/**
	 * @param IRecord &$dataSet
	 */
	abstract protected function doExtend( &$dataSet );

}
