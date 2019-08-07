<?php

namespace BlueSpice\Data\Entity;

use BlueSpice\EntityConfig;

interface IReader {
	/**
	 *
	 * @param mixed $id
	 * @param EntityConfig $entityConfig
	 * @return \stdClass
	 */
	public function resolveNativeDataFromID( $id, EntityConfig $entityConfig );
}
