<?php
/**
 * Hook handler base class for MediaWiki hook AuthManagerLoginAuthenticateAudit
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
 * @author     Oleksandr Pinchuk
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

abstract class AuthManagerLoginAuthenticateAudit extends Hook {

	/**
	 *
	 * @var AuthenticationResponse
	 */
	protected $response = null;

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var string
	 */
	protected $username = null;

	/**
	 * @param AuthenticationResponse $response
	 * @param User $user
	 * @param string $username
	 * @return mixed
	 */
	public static function callback( $response, $user, $username ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$response,
			$user,
			$username
		);
		return $hookHandler->process();
	}

	/**
	 * AuthManagerLoginAuthenticateAudit constructor.
	 * @param IContextSource $context
	 * @param Config $config
	 * @param AuthenticationResponse $response
	 * @param User $user
	 * @param string $username
	 */
	public function __construct( $context, $config, $response, $user, $username ) {
		parent::__construct( $context, $config );

		$this->response = $response;
		$this->user = $user;
		$this->username = $username;
	}
}
