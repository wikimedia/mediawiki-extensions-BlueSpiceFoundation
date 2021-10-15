<?php
/**
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
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

/**
 * DEPRECATED!
 * Enum BsPARAM
 * Option for BsCore::getParam().
 * @deprecated since version 3.1
 */
class BsPARAM {
	/**
	 * DEPRECATED!
	 * BsPARAM::REQUEST
	 * Use super global array $_REQUEST
	 * @deprecated since version 3.1
	 */
	public const REQUEST = 1;
	/**
	 * DEPRECATED!
	 * BsPARAM::GET
	 * Use super global array $_GET
	 * @deprecated since version 3.1
	 */
	public const GET = 2;
	/**
	 * DEPRECATED!
	 * BsPARAM::POST
	 * Use super global array $_POST
	 * @deprecated since version 3.1
	 */
	public const POST = 4;
	/**
	 * DEPRECATED!
	 * BsPARAM::FILES
	 * Use super global array $_FILES
	 * @deprecated since version 3.1
	 */
	public const FILES = 8;
	/**
	 * DEPRECATED!
	 * BsPARAM::COOKIE
	 * Use super global array $_COOKIE
	 * @deprecated since version 3.1
	 */
	public const COOKIE = 16;
	/**
	 * DEPRECATED!
	 * BsPARAM::SESSION
	 * Use super global array $_SESSION
	 * @deprecated since version 3.1
	 */
	public const SESSION = 32;
	/**
	 * DEPRECATED!
	 * BsPARAM::SERVER
	 * Use super global array $_SERVER
	 * @deprecated since version 3.1
	 */
	public const SERVER = 64;
}
