<?php

namespace BlueSpice\Data\Settings;

use MediaWiki\Context\IContextSource;
use MediaWiki\Json\FormatJson;
use MWStake\MediaWiki\Component\DataStore\DatabaseWriter;
use MWStake\MediaWiki\Component\DataStore\IReader;
use MWStake\MediaWiki\Component\DataStore\IRecord;

class Writer extends DatabaseWriter {

	/**
	 *
	 * @param IReader $reader
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 */
	public function __construct( IReader $reader, $loadBalancer,
		?IContextSource $context = null ) {
		parent::__construct( $reader, $loadBalancer, $context, $context->getConfig() );
	}

	/**
	 *
	 * @return string
	 */
	protected function getTableName() {
		return 'bs_settings3';
	}

	/**
	 *
	 * @param IRecord $record
	 * @return array
	 */
	protected function makeInsertFields( $record ) {
		$fields = parent::makeInsertFields( $record );
		$fields[Record::VALUE] = FormatJson::encode( $fields[Record::VALUE] );
		return $fields;
	}

	/**
	 *
	 * @param IRecord $existingRecord
	 * @param IRecord $record
	 * @return array
	 */
	protected function makeUpdateFields( $existingRecord, $record ) {
		$fields = parent::makeUpdateFields( $existingRecord, $record );
		$fields[Record::VALUE] = FormatJson::encode( $fields[Record::VALUE] );
		return $fields;
	}

	/**
	 * @return Schema Column definition compatible to
	 * https://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.Panel-cfg-columns
	 */
	public function getSchema() {
		return new Schema();
	}

	/**
	 *
	 * @return array
	 */
	protected function getIdentifierFields() {
		return [ Record::NAME ];
	}
}
