<?php

namespace BlueSpice\Data\Entity;

use BlueSpice\Entity;
use Exception;
use MediaWiki\Context\IContextSource;
use MediaWiki\Status\Status;
use MWStake\MediaWiki\Component\DataStore\RecordSet;

abstract class Writer implements IWriter, \MWStake\MediaWiki\Component\DataStore\IWriter {

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
	 * @param Entity $entity
	 * @return Status
	 */
	abstract public function writeEntity( Entity $entity );
}
