<?php
/**
 * Hook handler base class for BlueSpice hook BSApiExtJSStoreBaseBeforeReturnData
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

abstract class BSApiExtJSStoreBaseBeforeReturnData extends Hook {
	/**
	 * The store api
	 * @var \BSApiExtJSStoreBase
	 */
	protected $store = null;

	/**
	 * An array of data itemss
	 * @var \stdClass[]
	 */
	protected $dataItems = null;

	/**
	 * An array of meta data itemss
	 * @var \stdClass[]
	 */
	protected $metaDataItems = null;

	/**
	 * Located in BSApiExtJSStoreBase::returnData. Before the result gets
	 * returned
	 * @param \BSApiExtJSStoreBase $store
	 * @param array $dataItems
	 * @param array $metaDataItems
	 * @return boolean
	 */
	public static function callback( $store, &$dataItems, &$metaDataItems ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$store,
			$dataItems,
			$metaDataItems
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BSApiExtJSStoreBase $store
	 * @param array $dataItems
	 * @param array $metaDataItems
	 */
	public function __construct( $context, $config, $store, &$dataItems, &$metaDataItems ) {
		parent::__construct( $context, $config );

		$this->store = $store;
		$this->dataItems = &$dataItems;
		$this->metaDataItems = &$metaDataItems;
	}
}
