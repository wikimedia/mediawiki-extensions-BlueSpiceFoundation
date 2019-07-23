<?php

namespace BlueSpice\Data\Entity;

abstract class Reader extends \BlueSpice\Data\Reader implements IReader {

	public function getSchema() {
		return new Schema();
	}

}
