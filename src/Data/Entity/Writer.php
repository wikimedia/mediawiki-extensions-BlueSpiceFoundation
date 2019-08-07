<?php

namespace BlueSpice\Data\Entity;

use Exception;
use Status;
use IContextSource;
use BlueSpice\Entity;
use BlueSpice\Data\IWriter;
use BlueSpice\Data\RecordSet;

abstract class Writer implements IWriter {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param IContextSource $context
	 */
	public function __construct( IContextSource $context ) {
		$this->context = $context;
	}

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

	/**
	 *
	 * @param RecordSet $recordSet
	 * @throws Exception
	 */
	public function write( $recordSet ) {
		throw new Exception( 'write mode is not supported' );
	}

	/**
	 * Create or Update given records
	 * @param $entity Entity
	 * @return Status
	 */
	abstract public function writeEntity( Entity $entity );
}
