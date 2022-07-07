<?php

namespace BlueSpice\Data\Entity;

abstract class Reader extends \BlueSpice\Data\Reader implements IReader {

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

}
