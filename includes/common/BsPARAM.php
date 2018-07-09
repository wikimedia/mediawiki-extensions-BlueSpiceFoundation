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
 * For further information visit http://bluespice.com
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

/**
 * Enum BsPARAM
 * Option for BsCore::getParam().
 */
class BsPARAM {
	/**
	 * BsPARAM::REQUEST
	 * Use super global array $_REQUEST
	 */
	const REQUEST =  1;
	/**
	 * BsPARAM::GET
	 * Use super global array $_GET
	 */
	const GET     =  2;
	/**
	 * BsPARAM::POST
	 * Use super global array $_POST
	 */
	const POST    =  4;
	/**
	 * BsPARAM::FILES
	 * Use super global array $_FILES
	 */
	const FILES   =  8;
	/**
	 * BsPARAM::COOKIE
	 * Use super global array $_COOKIE
	 */
	const COOKIE  = 16;
	/**
	 * BsPARAM::SESSION
	 * Use super global array $_SESSION
	 */
	const SESSION = 32;
	/**
	 * BsPARAM::SERVER
	 * Use super global array $_SERVER
	 */
	const SERVER  = 64;
}
