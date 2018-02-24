<?php
/**
 * Hook handler base class for BlueSpice hook BSEntityGetFullData
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
use BlueSpice\Entity;

abstract class BSEntityGetFullData extends Hook {
	/**
	 * The Entity which stores the values
	 * @var Entity
	 */
	protected $entity = null;

	/**
	 * An array of values stored in the entity [ key => mixed value ].
	 * @var array
	 */
	protected $data = null;

	/**
	 * Located in \BlueSpice\Entity::getFullData. Before the full set of values
	 * stored in the entity is returned
	 * @param Entity $entity
	 * @param array $data
	 * @return boolean
	 */
	public static function callback( $entity, &$data ) {
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
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param Entity $entity
	 * @param array $data
	 */
	public function __construct( $context, $config, $entity, &$data ) {
		parent::__construct( $context, $config );

		$this->entity = $entity;
		$this->data = &$data;
	}
}
