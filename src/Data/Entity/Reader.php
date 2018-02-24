<?php

namespace BlueSpice\Data\Entity;

abstract class Reader extends \BlueSpice\Data\Reader {

	public function getSchema() {
		return new Schema();
	}
}
