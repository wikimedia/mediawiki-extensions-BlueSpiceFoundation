<?php
/**
 * Hook handler base class for BlueSpice hook
 * BSMigrateSettingsSetNewSettings
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSMigrateSettingsSetNewSettings extends Hook {


	/**
	 * New name of the setting
	 * @var string
	 */
	protected $newName = null;

	/**
	 *
	 * @var string - json formatted string of a mixed type
	 */
	protected $newValue = null;

	/**
	 * Whether the hook handler set the value for this setting
	 * @var boolean
	 */
	protected $set = false;

	/**
	 * Located in \BSMigrateSettings::saveConvertedData.
	 * Use it to apply new values for the system, eg. to $GLOBALS
	 * Handler should return false, once it sets the setting.
	 *
	 * @param string $newName
	 * @param string $newValue
	 * @param boolean $set
	 * @return boolean
	 */
	public static function callback( $newName, $newValue, &$set ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$newName,
			$newValue,
			$set
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $newName
	 * @param string $newValue
	 * @param boolean $set
	 */
	public function __construct( $context, $config, $newName, $newValue, &$set ) {
		parent::__construct( $context, $config );

		$this->newName = &$newName;
		$this->newValue = &$newValue;
		$this->set = &$set;
	}
}
