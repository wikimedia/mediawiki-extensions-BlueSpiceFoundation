<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlueSpice\Data\Entity;

use BlueSpice\Entity;
use MediaWiki\Status\Status;

interface IWriter {
	/**
	 * Create or Update given entity
	 * @param Entity $entity
	 * @return Status
	 */
	public function writeEntity( Entity $entity );
}
