<?php
/**
 * Hook handler base class for BlueSpice hook BSCoreConfigGet
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

abstract class BSCoreConfigGet extends Hook {

	/**
	 * The identifier of the variable
	 * @var string
	 */
	protected $path = null;

	/**
	 * The value of the variable
	 * @var mixed
	 */
	protected $returnResult = null;

	/**
	 * Located in BsConfig::get. Enables modification of the value of the
	 * BSConfig variable specified by path.
	 * @param string $path
	 * @param mixed $return
	 * @return boolean
	 */
	public static function callback( $path, &$returnResult ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$path,
			$returnResult
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $path
	 * @param mixed $return
	 */
	public function __construct( $context, $config, $path, &$returnResult ) {
		parent::__construct( $context, $config );

		$this->path = $path;
		$this->returnResult = &$returnResult;
	}
}
