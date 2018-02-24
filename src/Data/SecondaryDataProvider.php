<?php

namespace BlueSpice\Data;

use \BlueSpice\Data\ISecondaryDataProvider;

abstract class SecondaryDataProvider implements ISecondaryDataProvider {
	public function extend( $dataSets ) {
		foreach( $dataSets as &$dataSet ) {
			$this->doExtend( $dataSet );
		}

		return $dataSets;
	}

	protected abstract function doExtend( &$dataSet );

}
