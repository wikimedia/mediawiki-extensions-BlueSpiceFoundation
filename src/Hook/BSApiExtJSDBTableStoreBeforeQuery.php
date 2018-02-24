<?php
/**
 * Hook handler base class for BlueSpice hook BSApiExtJSDBTableStoreBeforeQuery
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSApiExtJSDBTableStoreBeforeQuery extends Hook {
	/**
	 *
	 * @var \BSApiExtJSDBTableStoreBase
	 */
	protected $store = null;

	/**
	 *
	 * @var string
	 */
	protected $queryString = null;

	/**
	 *
	 * @var array
	 */
	protected $filters = null;

	/**
	 *
	 * @var array
	 */
	protected $tables = null;

	/**
	 *
	 * @var array
	 */
	protected $fields = null;

	/**
	 *
	 * @var array
	 */
	protected $conditions = null;

	/**
	 *
	 * @var array
	 */
	protected $options = null;

	/**
	 *
	 * @var array
	 */
	protected $joinOptions = null;

	/**
	 *
	 * @var array
	 */
	protected $dataItems = null;

	/**
	 * Located in BSApiExtJSDBTableStoreBase::makeData. Before the database
	 * query gets executed
	 * @param \BSApiExtJSDBTableStoreBase $store
	 * @param string $queryString
	 * @param array $filters
	 * @param array $tables
	 * @param array $fields
	 * @param array $conditions
	 * @param array $options
	 * @param array $joinOptions
	 * @param array $dataItems
	 * @return boolean
	 */
	public static function callback( $store, $queryString, $filters, &$tables, &$fields, &$conditions, &$options, &$joinOptions, &$dataItems ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$store,
			$queryString,
			$filters,
			$tables,
			$fields,
			$conditions,
			$options,
			$joinOptions,
			$dataItems
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BSApiExtJSDBTableStoreBase $store
	 * @param string $queryString
	 * @param array $filters
	 * @param array $tables
	 * @param array $fields
	 * @param array $conditions
	 * @param array $options
	 * @param array $joinOptions
	 * @param array $dataItems
	 */
	public function __construct( $context, $config, $store, $queryString, $filters, &$tables, &$fields, &$conditions, &$options, &$joinOptions, &$dataItems ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->queryString = $queryString;
		$this->filters = $filters;
		$this->tables = &$tables;
		$this->fields = &$fields;
		$this->conditions = &$conditions;
		$this->options = &$options;
		$this->joinOptions = &$joinOptions;
		$this->dataItems = &$dataItems;
	}
}
