<?php
/**
 * Entity base class for BlueSpice
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
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use BlueSpice\Data\Entity\IStore;
use BlueSpice\Renderer\Entity as Renderer;
use BlueSpice\Renderer\Params;
use Exception;
use JsonSerializable;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Status\Status;
use MediaWiki\User\User;
use MWException;

abstract class Entity implements JsonSerializable {
	public const TYPE = '';

	public const ATTR_TYPE = 'type';
	public const ATTR_ID = 'id';
	public const ATTR_OWNER_ID = 'ownerid';
	public const ATTR_ARCHIVED = 'archived';
	public const ATTR_TIMESTAMP_CREATED = 'timestampcreated';
	public const ATTR_TIMESTAMP_TOUCHED = 'timestamptouched';

	/**
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 *
	 * @var EntityFactory
	 */
	protected $entityFactory = null;

	/**
	 *
	 * @var bool
	 */
	protected $bUnsavedChanges = true;

	/**
	 * @var EntityConfig
	 */
	protected $config = null;

	/**
	 *
	 * @var IStore
	 */
	protected $store = null;

	/**
	 * @var MediaWikiServices
	 */
	protected $services = null;

	/**
	 *
	 * @param \stdClass $stdClass
	 * @param EntityConfig $config
	 * @param EntityFactory $entityFactory
	 * @param IStore $store
	 */
	protected function __construct( \stdClass $stdClass, EntityConfig $config,
		EntityFactory $entityFactory, IStore $store ) {
		$this->entityFactory = $entityFactory;
		$this->config = $config;
		$this->store = $store;
		$this->services = MediaWikiServices::getInstance();
		if ( !empty( $stdClass->{static::ATTR_ID} ) ) {
			$this->attributes[static::ATTR_ID] =
				(int)$stdClass->{static::ATTR_ID};
		}
		if ( !empty( $stdClass->{static::ATTR_TYPE} ) ) {
			$this->attributes[static::ATTR_TYPE]
				= $stdClass->{static::ATTR_TYPE};
		} else {
			$this->attributes[static::ATTR_TYPE] = static::TYPE;
		}
		if ( !empty( $stdClass->{static::ATTR_ARCHIVED} ) ) {
			$this->attributes[static::ATTR_ARCHIVED] = true;
		}

		$this->setValuesByObject( $stdClass );
		if ( $this->exists() ) {
			$this->setUnsavedChanges( false );
		}
	}

	/**
	 * Returns an entity's attributes or the given default, if not set
	 * @param string $attrName
	 * @param mixed|null $default
	 * @return mixed
	 */
	public function get( $attrName, $default = null ) {
		if ( !isset( $this->attributes[$attrName] ) ) {
			return $default;
		}
		return $this->attributes[$attrName];
	}

	/**
	 * Returns the User object of the entity's owner
	 * @return User
	 */
	public function getOwner() {
		return $this->services->getUserFactory()
			->newFromId( $this->get( static::ATTR_OWNER_ID, 0 ) );
	}

	/**
	 * Returns the entity store
	 * @return IStore
	 * @throws MWException
	 */
	protected function getStore() {
		return $this->store;
	}

	/**
	 * Sets an entity's attributes
	 * @param string $attrName
	 * @param mixed $value
	 * @return Entity
	 */
	public function set( $attrName, $value ) {
		// An Entity's type should never be changed
		if ( $attrName == static::ATTR_TYPE ) {
			throw new MWException( "An Entity's type can not be changed!" );
		}

		$this->attributes[$attrName] = $value;
		return $this->setUnsavedChanges();
	}

	/**
	 * Returns the instance - Should not be used directly. Use mediawiki service
	 * 'BSEntityFactory' instead
	 * @param \stdClass $data
	 * @param EntityConfig $config
	 * @param IStore $store
	 * @param EntityFactory|null $entityFactory
	 * @return \static
	 */
	public static function newFromFactory( \stdClass $data, EntityConfig $config,
		IStore $store, ?EntityFactory $entityFactory = null ) {
		if ( !$entityFactory ) {
			$entityFactory = MediaWikiServices::getInstance()->getService(
				'BSEntityFactory'
			);
		}
		return new static( $data, $config, $entityFactory, $store );
	}

	/**
	 * Gets the related config object
	 * @return EntityConfig
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Saves the current Entity
	 * @param User|null $user
	 * @param array $aOptions
	 * @return Status
	 */
	public function save( ?User $user = null, $aOptions = [] ) {
		if ( !$user instanceof User ) {
			return Status::newFatal( 'No User' );
		}
		if ( $this->exists() && !$this->hasUnsavedChanges() ) {
			return Status::newGood( $this );
		}
		if ( empty( $this->get( static::ATTR_OWNER_ID, 0 ) ) ) {
			$this->set( static::ATTR_OWNER_ID, (int)$user->getId() );
		}
		if ( empty( $this->get( static::ATTR_TYPE ) ) ) {
			return Status::newFatal( 'Related Type error' );
		}

		try {
			$status = $this->getStore()->getWriter()->writeEntity( $this );
		} catch ( Exception $e ) {
			return Status::newFatal( $e->getMessage() );
		}
		// TODO: check why this is not good
		if ( !$status->isOK() ) {
			return $status;
		}

		$this->setUnsavedChanges( false );

		$this->services->getHookContainer()->run( 'BSEntitySaveComplete', [
			$this,
			$status,
			$user
		] );
		$this->invalidateCache();
		return $status;
	}

	/**
	 * Archives the current Entity
	 * @param User|null $user
	 * @return Status
	 */
	public function delete( ?User $user = null ) {
		$status = Status::newGood();

		$this->services->getHookContainer()->run( 'BSEntityDelete', [
			$this,
			$status,
			$user
		] );
		if ( !$status->isOK() ) {
			return $status;
		}
		$this->set( static::ATTR_ARCHIVED, true );
		$this->setUnsavedChanges();

		try {
			$status = $this->save( $user );
		} catch ( Exception $e ) {
			return Status::newFatal( $e->getMessage() );
		}

		$this->services->getHookContainer()->run( 'BSEntityDeleteComplete', [
			$this,
			$status,
			$user
		] );
		if ( !$status->isOK() ) {
			return $status;
		}

		$this->invalidateCache();
		return $status;
	}

	/**
	 * Restores the current Entity from archived state
	 * @param User|null $user
	 * @return Status
	 */
	public function undelete( ?User $user = null ) {
		$status = Status::newGood();

		$this->services->getHookContainer()->run( 'BSEntityUndelete', [
			$this,
			$status,
			$user
		] );
		if ( !$status->isOK() ) {
			return $status;
		}
		$this->set( static::ATTR_ARCHIVED, false );
		$this->setUnsavedChanges();

		try {
			$status = $this->save( $user );
		} catch ( Exception $e ) {
			return Status::newFatal( $e->getMessage() );
		}

		$this->services->getHookContainer()->run( 'BSEntityUndeleteComplete', [
			$this,
			$status,
			$user
		] );
		if ( !$status->isOK() ) {
			return $status;
		}

		$this->invalidateCache();
		return $status;
	}

	/**
	 * Gets the Entity attributes formated for the api
	 * @param array $data
	 * @return array
	 */
	public function getFullData( $data = [] ) {
		$data = array_merge(
			$data,
			[
				static::ATTR_ID => $this->get( static::ATTR_ID, 0 ),
				static::ATTR_OWNER_ID => $this->get( static::ATTR_OWNER_ID, 0 ),
				static::ATTR_TYPE => $this->get( static::ATTR_TYPE ),
				static::ATTR_ARCHIVED => $this->get( static::ATTR_ARCHIVED, false ),
				static::ATTR_TIMESTAMP_CREATED => $this->get(
					static::ATTR_TIMESTAMP_CREATED,
					Timestamp::getInstance()->getTimestamp( TS_MW )
				),
				static::ATTR_TIMESTAMP_TOUCHED => $this->get(
					static::ATTR_TIMESTAMP_TOUCHED,
					Timestamp::getInstance()->getTimestamp( TS_MW )
				),
			]
		);
		$this->services->getHookContainer()->run( 'BSEntityGetFullData', [
			$this,
			&$data
		] );
		return $data;
	}

	/**
	 *
	 * @param array $attribs
	 * @return Params
	 */
	protected function makeRendererParams( $attribs = [] ) {
		$attribs[Renderer::PARAM_ENTITY] = $this;
		return new Params( $attribs );
	}

	/**
	 *
	 * @param IContextSource|null $context
	 * @return Renderer
	 */
	public function getRenderer( ?IContextSource $context = null ) {
		if ( !$context ) {
			$context = RequestContext::getMain();
		}
		return $this->services->getService( 'BSRendererFactory' )->get(
			$this->getConfig()->get( 'Renderer' ),
			$this->makeRendererParams( [ Renderer::PARAM_CONTEXT => $context ] )
		);
	}

	/**
	 * Checks, if the current Entity exists in the Wiki
	 * @return bool
	 */
	public function exists() {
		return !empty( $this->get( static::ATTR_ID, 0 ) );
	}

	/**
	 * Checks if this entity is marked as archived
	 * @return bool
	 */
	public function isArchived() {
		return $this->get( static::ATTR_ARCHIVED, false ) !== false;
	}

	/**
	 * Checks if there are unsaved changes
	 * @return bool
	 */
	public function hasUnsavedChanges() {
		return (bool)$this->bUnsavedChanges;
	}

	/**
	 * Sets the current Entity to an unsaved changes mode, refreshes cache
	 * @param string $bStatus
	 * @return Entity
	 */
	public function setUnsavedChanges( $bStatus = true ) {
		$this->bUnsavedChanges = (bool)$bStatus;
		return $this;
	}

	/**
	 * Returns a json serializeable stdClass
	 * @return \stdClass
	 */
	public function jsonSerialize(): \stdClass {
		return (object)$this->getFullData();
	}

	/**
	 * @param \stdClass $data
	 */
	public function setValuesByObject( \stdClass $data ) {
		if ( !empty( $data->{static::ATTR_ARCHIVED} ) ) {
			$this->set( static::ATTR_ARCHIVED, $data->{static::ATTR_ARCHIVED} );
		}
		if ( !empty( $data->{static::ATTR_OWNER_ID} ) ) {
			$this->set( static::ATTR_OWNER_ID, $data->{static::ATTR_OWNER_ID} );
		}

		$this->services->getHookContainer()->run( 'BSEntitySetValuesByObject', [
			$this,
			$data
		] );
	}

	/**
	 * Checks if the given User is the owner of this entity
	 * @param User $user
	 * @return bool
	 */
	public function userIsOwner( User $user ) {
		if ( !$user->isRegistered() || $this->get( static::ATTR_OWNER_ID, 0 ) < 1 ) {
			return false;
		}
		return $user->getId() == $this->get( static::ATTR_OWNER_ID, 0 );
	}

	/**
	 * Invalidated the cache
	 * @return Entity
	 */
	public function invalidateCache() {
		$this->services->getHookContainer()->run( 'BSEntityInvalidate', [ $this ] );
		return $this;
	}
}
