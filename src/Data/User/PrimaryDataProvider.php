<?php

namespace BlueSpice\Data\User;

use BlueSpice\Data\IPrimaryDataProvider;
use BlueSpice\Data\FilterFinder;
use BlueSpice\Data\Filter;
use BlueSpice\Data\Filter\StringValue;
use BlueSpice\Data\Filter\Numeric;
use BlueSpice\Data\User\Schema;

class PrimaryDataProvider implements IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var \BlueSpice\Data\ReaderParams
	 */
	protected $params = null;
	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 */
	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 */
	public function makeData( $params ) {
		$this->data = [];
		$this->params = $params;
		$res = $this->db->select(
			'user',
			'*',
			$this->makePreFilterConds( $params ),
			__METHOD__,
			$this->makePreOptionConds( $params )
		);
		foreach( $res as $row ) {
			$this->appendRowToData( $row );
		}

		return $this->data;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 * @return array
	 */
	protected function makePreFilterConds( $params ) {
		$conds = [];
		$schema = new Schema();
		$fields = array_values( $schema->getFilterableFields() );
		$filterFinder = new FilterFinder( $params->getFilter() );
		foreach( $fields as $fieldName ) {
			$filter = $filterFinder->findByField( $fieldName );
			if( !$filter instanceof Filter ) {
				continue;
			}
			switch( $filter->getComparison() ) {
				case Filter::COMPARISON_EQUALS:
					$conds[$fieldName] = $filter->getValue();
					$filter->setApplied();
					break;
				case Filter::COMPARISON_NOT_EQUALS:
					$conds[] = "{$filter->getValue()} != $fieldName";
					$filter->setApplied();
					break;
				case StringValue::COMPARISON_CONTAINS:
					$conds[] = "$fieldName ".$this->db->buildLike(
						$this->db->anyString(),
						$filter->getValue(),
						$this->db->anyString()
					);
					$filter->setApplied();
					break;
				case StringValue::COMPARISON_NOT_CONTAINS:
					$conds[] = "$fieldName NOT ".$this->db->buildLike(
						$this->db->anyString(),
						$filter->getValue(),
						$this->db->anyString()
					);
					$filter->setApplied();
					break;
				case StringValue::COMPARISON_STARTS_WITH:
					$conds[] = "$fieldName ".$this->db->buildLike(
						$filter->getValue(),
						$this->db->anyString()
					);
					$filter->setApplied();
					break;
				case StringValue::COMPARISON_ENDS_WITH:
					$conds[] = "$fieldName ".$this->db->buildLike(
						$this->db->anyString(),
						$filter->getValue()
					);
					$filter->setApplied();
					break;
				case Numeric::COMPARISON_GREATER_THAN:
					$conds[] = "{$filter->getValue()} > $fieldName";
					$filter->setApplied();
					break;
				case Numeric::COMPARISON_LOWER_THAN:
					$conds[] = "{$filter->getValue()} < $fieldName";
					$filter->setApplied();
					break;
			}
		}
		return $conds;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 * @return array
	 */
	protected function makePreOptionConds( $params ) {
		$conds = [];

		$schema = new Schema();
		$fields = array_values( $schema->getSortableFields() );

		foreach( $params->getSort() as $sort ) {
			if( !in_array( $sort->getProperty(), $fields ) ) {
				continue;
			}
			if( !isset( $conds['ORDER BY'] ) ) {
				$conds['ORDER BY'] = "";
			} else {
				$conds['ORDER BY'] .= ",";
			}
			$conds['ORDER BY'] .=
				"{$sort->getProperty()} {$sort->getDirection()}";
		}
		return $conds;
	}

	protected function appendRowToData( $row ) {
		if( $this->params->getQuery() !== '' ) {
			$bApply = \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$row->{Record::USER_NAME},
				$this->params->getQuery()
			) || \BsStringHelper::filter(
				\BsStringHelper::FILTER_CONTAINS,
				$row->{Record::USER_REAL_NAME},
				$this->params->getQuery()
			);
			if( !$bApply ) {
				return;
			}
		}

		$this->data[] = new Record( (object) [
			Record::ID => $row->{Record::ID},
			Record::USER_NAME => $row->{Record::USER_NAME},
			Record::USER_REAL_NAME => empty( $row->{Record::USER_REAL_NAME} )
				? $row->{Record::USER_NAME}
				: $row->{Record::USER_REAL_NAME},
		] );
	}
}
