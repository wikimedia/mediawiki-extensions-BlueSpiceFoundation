<?php
/**
 * Hook handler base class for MediaWiki hook WebResponseSetCookie
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
 * For further information visit https://bluespice.com
 *
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class WebResponseSetCookie extends Hook {

	/**
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 *
	 * @var string
	 */
	protected $value = '';

	/**
	 *
	 * @var int
	 */
	protected $expire = -1;

	/**
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * @param string &$name
	 * @param string &$value
	 * @param int &$expire
	 * @param array &$options
	 * @return bool
	 */
	public static function callback( &$name, &$value, &$expire, &$options ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$name,
			$value,
			$expire,
			$options
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string &$name
	 * @param string &$value
	 * @param int &$expire
	 * @param array &$options
	 */
	public function __construct( $context, $config, &$name, &$value, &$expire, &$options ) {
		parent::__construct( $context, $config );

		$this->name =& $name;
		$this->value =& $value;
		$this->expire =& $expire;
		$this->options =& $options;
	}
}
