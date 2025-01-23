<?php
/**
 * Hook handler base class for MediaWiki hook ImgAuthBeforeStream
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
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;

abstract class ImgAuthBeforeStream extends Hook {

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var File
	 */
	protected $path = null;

	/**
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 *
	 * @var array
	 */
	protected $result = null;

	/**
	 *
	 * @param Title &$title
	 * @param string &$path
	 * @param string &$name
	 * @param array &$result
	 * @return bool
	 */
	public static function callback( &$title, &$path, &$name, &$result ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$path,
			$name,
			$result
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title &$title
	 * @param string &$path
	 * @param string &$name
	 * @param array &$result
	 */
	public function __construct( $context, $config, &$title, &$path, &$name, &$result ) {
		parent::__construct( $context, $config );

		$this->title = &$title;
		$this->path = &$path;
		$this->name = &$name;
		$this->result = &$result;
	}
}
