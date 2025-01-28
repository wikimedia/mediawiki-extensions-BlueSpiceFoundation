<?php
/**
 * Hook handler base class for BlueSpice hook BSEntitySaveComplete
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
use MediaWiki\Status\Status;
use MediaWiki\User\User;

abstract class BSEntitySaveComplete extends Hook {
	/**
	 * The Entity which was saved
	 * @var Entity
	 */
	protected $entity = null;

	/**
	 *
	 * @var Status
	 */
	protected $status = null;

	/**
	 * User who performed this action
	 * @var User
	 */
	protected $user = null;

	/**
	 * Located in \BlueSpice\Entity::save. After the entity was saved
	 * successful.
	 * @param Entity $entity
	 * @param Status $status
	 * @param User $user
	 * @return bool
	 */
	public static function callback( $entity, $status, $user ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$entity,
			$status,
			$user
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Entity $entity
	 * @param Status $status
	 * @param User $user
	 */
	public function __construct( $context, $config, $entity, $status, $user ) {
		parent::__construct( $context, $config );

		$this->entity = $entity;
		$this->status = $status;
		$this->user = $user;
	}
}
