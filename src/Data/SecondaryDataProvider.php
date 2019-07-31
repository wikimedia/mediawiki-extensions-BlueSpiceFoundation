<?php

namespace BlueSpice\Data;

abstract class SecondaryDataProvider implements ISecondaryDataProvider {
	public function extend( $dataSets ) {
		foreach ( $dataSets as &$dataSet ) {
			$this->doExtend( $dataSet );
		}

		return $dataSets;
	}

	abstract protected function doExtend( &$dataSet );

}
