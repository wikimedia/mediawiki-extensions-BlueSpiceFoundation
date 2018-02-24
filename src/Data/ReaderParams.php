<?php

namespace BlueSpice\Data;

use BlueSpice\Data\Sort;
use BlueSpice\Data\Filter;

class ReaderParams {
	const LIMIT_INFINITE = -1;

	/**
	 * For pre filtering
	 * @var string
	 */
	protected $query = '';

	/**
	 * For paging
	 * @var int
	 */
	protected $start = 0;

	/**
	 * For paging
	 * @var int
	 */
	protected $limit = 25;

	/**
	 *
	 * @var Sort[]
	 */
	protected $sort = [];

	/**
	 *
	 * @var Filter[]
	 */
	protected $filter = [];

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params = [] ) {
		$this->setIfAvailable( $this->query, $params, 'query' );
		$this->setIfAvailable( $this->start, $params, 'start' );
		$this->setIfAvailable( $this->limit, $params, 'limit' );
		$this->setSort( $params );
		$this->setFilter( $params );
	}

	protected function setIfAvailable( &$property, $source, $field ) {
		if( isset( $source[$field] ) ) {
			$property = $source[$field];
		}
	}

	/**
	 * Getter for "limit" param
	 * @return int The "limit" parameter
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Getter for "start" param
	 * @return int The "start" parameter
	 */
	public function getStart() {
		//TODO: mabye this can be calculated from "page" and "limit"; Examine behavior of Ext.data.Store / Ext.data.Proxy
		return $this->start;
	}

	/**
	 * Getter for "sort" param
	 * @return Sort[]
	 */
	public function getSort() {
		return $this->sort;
	}

	/**
	 * Getter for "query" param
	 * @return string The "query" parameter
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Getter for "filter" param
	 * @return Filter[]
	 */
	public function getFilter() {
		return $this->filter;
	}

	protected function setSort( $params ) {
		if( !isset( $params['sort'] ) || !is_array(  $params['sort'] ) ) {
			return;
		}

		$this->sort = Sort::newCollectionFromArray( $params['sort'] );
	}

	protected function setFilter( $params ) {
		if( !isset( $params['filter'] ) || !is_array(  $params['filter'] ) ) {
			return;
		}
		$this->filter = Filter::newCollectionFromArray( $params['filter'] );
	}

}
