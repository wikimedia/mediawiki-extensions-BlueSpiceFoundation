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
 * Enum BsPARAMTYPE
 * Option for BsCore::getParam(), BsCore::sanitize() and
 * BsCore::sanitizeArrayEntry().
 * @deprecated since version 3.1
 */
class BsPARAMTYPE {
	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::RAW
	 * No type checking.
	 * @deprecated since version 3.1
	 */
	public const RAW = 64;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::INT
	 * Parameter has to be of type Int/Integer.
	 * @deprecated since version 3.1
	 */
	public const INT = 128;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::FLOAT
	 * Parameter has to be of type Float.
	 * @deprecated since version 3.1
	 */
	public const FLOAT = 256;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::NUMERIC
	 * Parameter has to be numeric.
	 * @deprecated since version 3.1
	 */
	public const NUMERIC = 512;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::BOOL
	 * Parameter has to be of type Boolean.
	 * @deprecated since version 3.1
	 */
	public const BOOL = 1024;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::STRING
	 * Parameter has to be of type String.
	 * @deprecated since version 3.1
	 */
	public const STRING = 2048;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::SQL_STRING
	 * Parameter has to be of type String.
	 * Several operations will be executed to prevent SQL injection by this
	 * value.
	 * @deprecated since version 3.1
	 */
	public const SQL_STRING = 4096;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::ARRAY_MIXED
	 * Parameter has to be an Array.
	 * There will be no type checking of the contained values.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_MIXED = 8192;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::INT
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Int/Integer.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_INT = 16384;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::ARRAY_FLOAT
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Float.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_FLOAT = 32768;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::ARRAY_NUMERIC
	 * Parameter has to be an Array.
	 * Each contained value has to be numeric.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_NUMERIC = 65536;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::ARRAY_BOOL
	 * Parameter has to be an Array.
	 * Each contained value has to be of type Boolean.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_BOOL = 131072;

	/**
	 * DEPRECATED!
	 * BsPARAMTYPE::ARRAY_STRING
	 * Parameter has to be an Array.
	 * Each contained value has to be of type String.
	 * @deprecated since version 3.1
	 */
	public const ARRAY_STRING = 262144;
}
