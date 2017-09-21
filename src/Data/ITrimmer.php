<?php

 namespace BlueSpice\Data;

 interface ITrimmer {

	 /**
	  *
	  * @param \BlueSpice\Data\Record[] $dataSets
	  * @return \BlueSpice\Data\Record[]
	  */
	 public function trim( $dataSets );
}
