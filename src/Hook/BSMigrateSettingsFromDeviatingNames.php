<?php
/**
 * Hook handler base class for BlueSpice hook
 * BSMigrateSettingsFromDeviatingNames
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
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSMigrateSettingsFromDeviatingNames extends Hook {

	/**
	 * Old name of the setting, that gets migrated
	 * @var string
	 */
	protected $oldName = null;

	/**
	 * New name of the setting
	 * @var string
	 */
	protected $newName = null;

	/**
	 *
	 * @var string - serialized string of a mixed type
	 */
	protected $oldValue = null;

	/**
	 *
	 * @var string - json formated string of a mixed type
	 */
	protected $newValue = null;

	/**
	 * Skip migration of the current setting
	 * @var boolean 
	 */
	protected $skip = null;

	/**
	 * Located in \BSMigrateSettings::fromDeviatingNames. Use change the new
	 * name of the setting.
	 * @param string $oldName
	 * @param string $newName
	 * @return boolean
	 */
	public static function callback( $oldName, &$newName, $oldValue, &$newValue, &$skip ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$oldName,
			$newName,
			$oldValue,
			$newValue,
			$skip
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $oldName
	 * @param string $newName
	 */
	public function __construct( $context, $config, $oldName, &$newName, $oldValue, &$newValue, &$skip ) {
		parent::__construct( $context, $config );

		$this->oldName = $oldName;
		$this->newName = &$newName;
		$this->oldValue = $oldValue;
		$this->newValue = &$newValue;
		$this->skip = &$skip;
	}
}
