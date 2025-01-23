<?php
/**
 * Hook handler base class for BlueSpice hook BSEntitySetValuesByObject
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

use BlueSpice\Entity;
use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class BSEntitySetValuesByObject extends Hook {
	/**
	 * The Entity which should store the values
	 * @var Entity
	 */
	protected $entity = null;

	/**
	 * An obect of values to store in the entity (object)[ key => mixed value ].
	 * @var \stdClass
	 */
	protected $data = null;

	/**
	 * Located in \BlueSpice\Entity::setValuesByObject. After the known values
	 * are stored in the entity
	 * @param Entity $entity
	 * @param \stdClass $data
	 * @return bool
	 */
	public static function callback( $entity, $data ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$entity,
			$data
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Entity $entity
	 * @param \stdClass $data
	 */
	public function __construct( $context, $config, $entity, $data ) {
		parent::__construct( $context, $config );

		$this->entity = $entity;
		$this->data = $data;
	}
}
