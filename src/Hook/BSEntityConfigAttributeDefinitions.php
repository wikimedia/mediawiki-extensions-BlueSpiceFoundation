<?php
/**
 * Hook handler base class for BlueSpice hook BSEntityConfigAttributeDefinitions
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
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class BSEntityConfigAttributeDefinitions extends Hook {
	/**
	 *
	 * @var \BlueSpice\EntityConfig
	 */
	protected $entityConfig = null;

	/**
	 * An array of attribute array definitions [ key => [ key => value] ]
	 * @var array
	 */
	protected $attributeDefinitions = null;

	/**
	 * Located in \BlueSpice\EntityConfig::get_AttributeDefinitions. Use this
	 * hook to inject entity attribute definitions.
	 * @param \BlueSpice\EntityConfig $entityConfig
	 * @param array &$attributeDefinitions
	 * @return bool
	 */
	public static function callback( \BlueSpice\EntityConfig $entityConfig, &$attributeDefinitions ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$entityConfig,
			$attributeDefinitions
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param \BlueSpice\EntityConfig $entityConfig
	 * @param array &$attributeDefinitions
	 */
	public function __construct( $context, $config, $entityConfig, &$attributeDefinitions ) {
		parent::__construct( $context, $config );

		$this->entityConfig = $entityConfig;
		$this->attributeDefinitions = &$attributeDefinitions;
	}
}
