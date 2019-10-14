<?php

namespace BlueSpice\Data;

abstract class DatabaseWriter extends Writer {

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var IReader
	 */
	protected $reader = null;

	/**
	 * @return array
	 */
	abstract protected function getIdentifierFields();

	/**
	 * @return string
	 */
	abstract protected function getTableName();

	/**
	 *
	 * @param IReader $reader
	 * @param \Wikimedia\Rdbms\LoadBalancer $loadBalancer
	 * @param \IContextSource|null $context
	 * @param \Config|null $config
	 */
	public function __construct( IReader $reader, $loadBalancer,
		\IContextSource $context = null, \Config $config = null ) {
		parent::__construct( $context, $config );
		$this->reader = $reader;
		$this->db = $loadBalancer->getConnection( DB_MASTER );
	}

	/**
	 * Create or Update given records
	 * @param RecordSet $recordSet
	 * @return RecordSet
	 */
	public function write( $recordSet ) {
		foreach ( $recordSet->getRecords() as $record ) {
			if ( !$record->getStatus()->isOK() ) {
				continue;
			}
			$existingRecord = $this->getExistingRecord( $record );
			if ( !$existingRecord ) {
				$this->insert( $record );
				continue;
			}
			$this->modify( $existingRecord, $record );
		}
		return $recordSet;
	}

	/**
	 * Remove given records
	 * @param RecordSet $recordSet
	 * @return RecordSet
	 */
	public function remove( $recordSet ) {
		foreach ( $recordSet->getRecords() as $record ) {
			if ( !$record->getStatus()->isOK() ) {
				continue;
			}
			$existingRecord = $this->getExistingRecord( $record );
			if ( !$existingRecord ) {
				$record->getStatus()->fatal(
					"Record not found in table: " . $this->getTableName()
				);
				continue;
			}
			$this->delete( $existingRecord, $record );
		}
		return $recordSet;
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $record
	 * @return void
	 */
	protected function insert( $record ) {
		$record->getData();
		try {
			$success = $this->db->insert(
				$this->getTableName(),
				$this->makeInsertFields( $record ),
				__METHOD__
			);
		} catch ( \Exception $e ) {
			$record->getStatus()->fatal( $e );
			return;
		}
		if ( !$success ) {
			$record->getStatus()->fatal(
				"Error writing into: " . $this->getTableName()
			);
		}
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 * @return void
	 */
	protected function modify( $existingRecord, $record ) {
		$record->getData();
		try {
			$success = $this->db->update(
				$this->getTableName(),
				$this->makeUpdateFields( $existingRecord, $record ),
				$this->makeUpdateConditions( $existingRecord, $record ),
				__METHOD__
			);
		} catch ( \Exception $e ) {
			$record->getStatus()->fatal( $e );
			return;
		}
		if ( !$success ) {
			$record->getStatus()->fatal(
				"Error writing into: " . $this->getTableName()
			);
		}
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 * @return void
	 */
	protected function delete( $existingRecord, $record ) {
		$record->getData();
		try {
			$success = $this->db->delete(
				$this->getTableName(),
				$this->makeDeleteConditions( $existingRecord, $record ),
				__METHOD__
			);
		} catch ( \Exception $e ) {
			$record->getStatus()->fatal( $e );
			return;
		}
		if ( !$success ) {
			$record->getStatus()->fatal(
				"Error deleting from: " . $this->getTableName()
			);
		}
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $record
	 * @return array
	 */
	protected function makeInsertFields( $record ) {
		return (array)$record->getData();
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 * @return array
	 */
	protected function makeUpdateFields( $existingRecord, $record ) {
		$return = [];
		foreach ( (array)$record->getData() as $fieldName => $mValue ) {
			if ( in_array( $fieldName, $this->getIdentifierFields() ) ) {
				continue;
			}
			$return[$fieldName] = $mValue;
		}
		return $return;
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 * @return array
	 */
	protected function makeUpdateConditions( $existingRecord, $record ) {
		$return = [];
		foreach ( $this->getIdentifierFields() as $fieldName ) {
			$return[$fieldName] = $existingRecord->get( $fieldName );
		}
		return $return;
	}

	/**
	 *
	 * @param \BlueSpice\Data\IRecord $existingRecord
	 * @param \BlueSpice\Data\IRecord $record
	 * @return array
	 */
	protected function makeDeleteConditions( $existingRecord, $record ) {
		$return = [];
		foreach ( $this->getIdentifierFields() as $fieldName ) {
			$return[$fieldName] = $existingRecord->get( $fieldName );
		}
		return $return;
	}

	/**
	 *
	 * @param BlueSpice\Data\Record $record
	 * @return BlueSpice\Data\Record
	 */
	protected function getExistingRecord( $record ) {
		$recordSet = $this->reader->read( new ReaderParams( [
			ReaderParams::PARAM_LIMIT => ReaderParams::LIMIT_INFINITE,
			ReaderParams::PARAM_FILTER => $this->makeExistingRecordFilters( $record )
		] ) );
		$records = $recordSet->getRecords();
		if ( count( $records ) < 1 ) {
			return false;
		}
		return $records[0];
	}

	/**
	 *
	 * @param BlueSpice\Data\Record $record
	 * @return array
	 */
	protected function makeExistingRecordFilters( $record ) {
		$filters = [];
		foreach ( $this->getIdentifierFields() as $fieldName ) {
			$filters[] = $this->makeExistingRecordFilter(
				$record,
				$fieldName
			);
		}
		return $filters;
	}

	/**
	 *
	 * @param BlueSpice\Data\Record $record
	 * @param string $fieldName
	 * @return array
	 */
	protected function makeExistingRecordFilter( $record, $fieldName ) {
		return [
			Filter::KEY_FIELD => $fieldName,
			Filter::KEY_VALUE => $record->get( $fieldName ),
			Filter::KEY_TYPE => $this->getFilterTypeFromFieldMapping( $fieldName ),
			Filter::KEY_COMPARISON => Filter::COMPARISON_EQUALS,
		];
	}

	/**
	 * Returns the field type defined in the related schema
	 * @param string $fieldName
	 * @return string
	 */
	protected function getFieldType( $fieldName ) {
		$schema = $this->getSchema();
		return $schema[$fieldName][Schema::TYPE];
	}

	/**
	 * Field types are not equal filter names. Field type 'int' would be filter
	 * 'numeric'. Overwrite this for special fields or when you use your own
	 * filters
	 * @param string $fieldName
	 * @return string
	 */
	protected function getFilterTypeFromFieldMapping( $fieldName ) {
		$fieldType = $this->getFieldType( $fieldName );
		if ( $fieldType === FieldType::INT ) {
			return 'numeric';
		}
		if ( $fieldType === FieldType::FLOAT ) {
			return 'numeric';
		}
		return $fieldType;
	}
}
