<?php
/**
 * Hook handler base class for MediaWiki hook UserCan
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
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use IContextSource;
use Config;
use User;
use Title;
use BlueSpice\Hook;

abstract class UserCan extends Hook {

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 *
	 * @var boolean
	 */
	protected $result = false;

	/**
	 *
	 * @param Title &$title
	 * @param User &$user
	 * @param string $action
	 * @param bool &$result
	 * @return bool
	 */
	public static function callback( &$title, &$user, $action, &$result ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$user,
			$action,
			$result
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title &$title
	 * @param User &$user
	 * @param string $action
	 * @param bool &$result
	 */
	public function __construct( $context, $config, &$title, &$user, $action, &$result ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->user = $user;
		$this->action = $action;
		$this->result =& $result;
	}
}
