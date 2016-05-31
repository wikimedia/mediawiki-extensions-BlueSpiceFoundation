<?php
/**
 * This file contains most Enum classes used within the BlueSpice framework.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @version    1.1.0

 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
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

/**
 * BsPARAMOPTION
 *
 * Options for BsCore::sanitize(), BsCore::sanitizeArrayEntry()
 * and BsCore::getParam()
 */
class BsPARAMOPTION {
	/**
	 * BsPARAMOPTION::DEFAULT_ON_ERROR
	 *
	 * For use with BsCore::getParam(). If type checking results in an error,
	 * the provided default value is returned.
	 * By default it is attempted to sanitize the value, even if type checking
	 * fails.
	 */
	const DEFAULT_ON_ERROR = 524288;

	/**
	 * BsPARAMOPTION::CLEANUP_STRING
	 * Attempt to sanitize the value, even if type checking
	 * fails.
	 */
	const CLEANUP_STRING   = 1048576;
}

/**
 * BsPATHTYPE
 *
 * When creating pathes within the BsCore framework, use PATHTYPE to specify
 * what kind of path should be created.
 */
class BsPATHTYPE {
	/**
	 * BsPATHTYPE::ABSOLUTE
	 * An absolute path is created.
	 * For example "http://www.hallowelt.biz/path/to/some/file.html".
	 */
	const ABSOLUTE = 0;

	/**
	 * BsPATHTYPE::RELATIVE
	 * A relative path is created.
	 * For example "path/to/some/file.html".
	 */
	const RELATIVE = 1;
}

/**
 * BsRUNLEVEL
 *
 * Defines different runlevels of the BlueSpice framework.
 */
class BsRUNLEVEL {
	/**
	 * BsRUNLEVEL::FULL
	 * Load all extensions. This is used for normal view of the website.
	 */
	const FULL   = 1;
	
	/**
	 * BsRUNLEVEL::REMOTE
	 * To save server resources there are only a few extensions to be loaded.
	 * Used for API/Webservice and AJAX calls.
	 */
	const REMOTE = 2;
}

/**
 * BsACTION
 *
 * Represents the different actions a http request can contain. Those are
 * equivalent to the allowed values of the "action" parameter within a
 * querystring.
 * With BlueSpice being a former MediaWiki framework those actions are very
 * similar to the ones described at
 * http://www.mediawiki.org/wiki/Manual:Parameters_to_index.php
 */
class BsACTION {
	const NONE = 0;
	/**
	 * BsACTION::LOAD_SPECIALPAGE
	 * Has to be set, when a extension provides a specialpage
	 */
	const LOAD_SPECIALPAGE = 1;
	/**
	 * BsACTION::LOAD_ON_API
	 * Has to be set, when a extension provides an api module
	 */
	const LOAD_ON_API = 2;
}

/**
 * BsSTYLEMEDIA
 * Represents the different values for the 'media' attribute of a <link />-tag.
 */
class BsSTYLEMEDIA {
	/**
	 * BsSTYLEMEDIA::ALL
	 * This style applies to all types of media.
	 */
	const ALL        = 255;

	/**
	 * BsSTYLEMEDIA::AURAL
	 * This style is used for audible output.
	 */
	const AURAL      =   1;

	/**
	 * BsSTYLEMEDIA::BRAILLE
	 * This style is used for embossed printing.
	 */
	const BRAILLE    =   2;

	/**
	 * BsSTYLEMEDIA::HANDHELD
	 * This style applies handheld comupters, mobild phones and similar devices.
	 */
	const HANDHELD   =   4;

	/**
	 * BsSTYLEMEDIA::PRINTER
	 * This style applies to all types of media.
	 */
	const PRINTER    =   8;

	/**
	 * BsSTYLEMEDIA::PROJECTION
	 * This style is used for projectors.
	 */
	const PROJECTION =  16;

	/**
	 * BsSTYLEMEDIA::SCREEN
	 * This style is used for computer screens. In most cases this is the
	 * setting of choice.
	 */
	const SCREEN     =  32;

	/**
	 * BsSTYLEMEDIA::TTY
	 * This style applies to teletypewriter .
	 */
	const TTY        =  64;

	/**
	 * BsSTYLEMEDIA::TV
	 * This style is used for television.
	 */
	const TV         = 128;
}

class EXTINFO {
	const NAME        = 0;
	const DESCRIPTION = 1;
	const AUTHOR      = 2;
	const VERSION     = 3;
	const STATUS      = 4;
	const DEPS        = 5;
	const URL         = 6;
	const PACKAGE     = 7;
}

// TODO MRG20100810: Die Typen sind eigentlich Mediawiki-spezifisch. Ebenso die Actions weiter oben.
// Sollten wir die nicht zum Mediawiki-Teil packen?
class EXTTYPE {
	/**
	 * EXTTYPE::SPECIALPAGE
	 * Reserved for additions to MediaWiki Special Pages.
	 */
	const SPECIALPAGE = 'specialpage';
	/**
	 * EXTTYPE::PARSERHOOK
	 * Used if your extension modifies, complements, or replaces the parser functions in MediaWiki.
	 */
	const PARSERHOOK  = 'parserhook';
	/**
	 * EXTTYPE::VARIABLE
	 * Extension that add multiple functionality to MediaWiki.
	 */
	const VARIABLE    = 'variable';
	/**
	 * EXTTYPE::OTHER
	 * All other extensions.
	 */
	const OTHER       = 'other';
}
