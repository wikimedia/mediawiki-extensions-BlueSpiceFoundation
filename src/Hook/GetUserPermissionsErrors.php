<?php
/**
 * Hook handler base class for MediaWiki hook getUserPermissionsErrors
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
use MediaWiki\Title\Title;
use MediaWiki\User\User;

abstract class GetUserPermissionsErrors extends Hook {
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
	protected $action = null;

	/**
	 *
	 * @var array
	 */
	protected $result = null;

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param array &$result
	 * @return bool
	 */
	public static function callback( $title, $user, $action, &$result ) {
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
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param array &$result
	 */
	public function __construct( $context, $config, $title, $user, $action, &$result ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->user = $user;
		$this->action = $action;
		$this->result = &$result;
	}
}
