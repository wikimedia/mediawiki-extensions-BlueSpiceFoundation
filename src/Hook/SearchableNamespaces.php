<?php
/**
 * Hook handler base class for MediaWiki hook SearchableNamespaces
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class SearchableNamespaces extends Hook {

	/**
	 *
	 * @var array
	 */
	protected $namespaces;

	/**
	 *
	 * @param array $namespaces
	 * @return boolean
	 */
	public static function callback( &$namespaces ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$namespaces
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param array $namespaces
	 * @param boolean $result
	 */
	public function __construct( $context, $config, &$namespaces ) {
		parent::__construct( $context, $config );

		$this->namespaces = &$namespaces;
	}
}
