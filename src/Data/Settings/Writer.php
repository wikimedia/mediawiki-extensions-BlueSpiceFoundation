<?php

namespace BlueSpice\Data\Settings;
use BlueSpice\Data\RecordSet;

class Writer extends \BlueSpice\Data\DatabaseWriter {

	/**
	 *
	 * @param \BlueSpice\Data\IReader $reader
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param \IContextSource|null $context
	 */
	public function __construct( \BlueSpice\Data\IReader $reader, $loadBalancer, \IContextSource $context = null ) {
		parent::__construct( $reader, $loadBalancer, $context, $context->getConfig() );
	}

	protected function getTableName() {
		return 'bs_settings3';
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $record
	 */
	protected function makeInsertFields( $record ) {
		$fields = parent::makeInsertFields( $record );
		$fields[Record::VALUE] = \FormatJson::encode( $fields[Record::VALUE] );
		return $fields;
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 */
	protected function makeUpdateFields( $existingRecord, $record ) {
		$fields = parent::makeUpdateFields( $existingRecord, $record );
		$fields[Record::VALUE] = \FormatJson::encode( $fields[Record::VALUE] );
		return $fields;
	}

	/**
	 * @return Schema Column definition compatible to
	 * https://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.Panel-cfg-columns
	 */
	public function getSchema() {
		return new Schema();
	}

	protected function getIdentifierFields() {
		return [ Record::NAME ];
	}
}
