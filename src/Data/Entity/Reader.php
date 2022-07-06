<?php

namespace BlueSpice\Data\Entity;

abstract class Reader extends \MWStake\MediaWiki\Component\DataStore\Reader implements IReader {

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

}
