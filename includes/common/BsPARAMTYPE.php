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
 * Enum BsPARAMTYPE
 * Option for BsCore::getParam(), BsCore::sanitize() and
 * BsCore::sanitizeArrayEntry().
 */
class BsPARAMTYPE {
	/**
	 * BsPARAMTYPE::RAW
	 * No type checking.
	 */
	const RAW           =    64;

	/**
	 * BsPARAMTYPE::INT
	 * Parameter has to be of type Int/Integer.
	 */
	const INT           =    128;

	/**
	 * BsPARAMTYPE::FLOAT
	 * Parameter has to be of type Float.
	 */
	const FLOAT         =    256;

	/**
	 * BsPARAMTYPE::NUMERIC
	 * Parameter has to be numeric.
	 */
	const NUMERIC       =    512;

	/**
	 * BsPARAMTYPE::BOOL
	 * Parameter has to be of type Boolean.
	 */
	const BOOL          =    1024;

	/**
	 * BsPARAMTYPE::STRING
	 * Parameter has to be of type String.
	 */
	const STRING        =   2048;

	/**
	 * BsPARAMTYPE::SQL_STRING
	 * Parameter has to be of type String.
	 * Several operations will be executed to prevent SQL injection by this
	 * value.
	 */
	const SQL_STRING    =   4096;

	/**
	 * BsPARAMTYPE::ARRAY_MIXED
	 * Parameter has to be an Array.
	 * There will be no type checking of the contained values.
	 */
	const ARRAY_MIXED   =   8192;

	/**
	 * BsPARAMTYPE::INT
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Int/Integer.
	 */
	const ARRAY_INT     =  16384;

	/**
	 * BsPARAMTYPE::ARRAY_FLOAT
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Float.
	 */
	const ARRAY_FLOAT   =  32768;

	/**
	 * BsPARAMTYPE::ARRAY_NUMERIC
	 * Parameter has to be an Array.
	 * Each contained value has to be numeric.
	 */
	const ARRAY_NUMERIC =  65536;

	/**
	 * BsPARAMTYPE::ARRAY_BOOL
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Boolean.
	 */
	const ARRAY_BOOL    = 131072;

	/**
	 * BsPARAMTYPE::ARRAY_STRING
	 * Parameter has to be an Array.
	 * Each contained value has to be of type String.
	 */
	const ARRAY_STRING  = 262144;
}
