<?php

/**
 * Hook handler base class for MediaWiki hook MessagesPreLoad
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
 * @author     Peter Boehm <boehm@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class MessagesPreLoad extends Hook {

	/**
	 * title of the message
	 * @var string
	 */
	protected $title = '';

	/**
	 * the message to define
	 * @var string
	 */
	protected $message = '';

	/**
	 * language code being requested
	 * @var string
	 */
	protected $code = '';

	/**
	 *
	 * @param string $title
	 * @param string &$message
	 * @param string $code
	 * @return bool
	 */
	public static function callback( $title, &$message, $code ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$message,
			$code
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string $title
	 * @param string &$message
	 * @param string $code
	 */
	public function __construct( $context, $config, $title, &$message, $code ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->message =& $message;
		$this->code = $code;
	}

}
