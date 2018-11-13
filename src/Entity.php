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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;
use BlueSpice\Content\Entity as EntityContent;
use MediaWiki\MediaWikiServices;
use BlueSpice\Renderer\Entity as Renderer;
use BlueSpice\Renderer\Params;

abstract class Entity implements \JsonSerializable {
	const NS = -1;
	const TYPE = '';

	const ATTR_TYPE = 'type';
	const ATTR_ID = 'id';
	const ATTR_OWNER_ID = 'ownerid';
	const ATTR_ARCHIVED = 'archived';
	const ATTR_PARENT_ID = 'parentid';
	const ATTR_TIMESTAMP_CREATED = 'timestampcreated';
	const ATTR_TIMESTAMP_TOUCHED = 'timestamptouched';

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
	 * @var boolean
	 */
	protected $bUnsavedChanges = true;

	/**
	 * @var EntityConfig
	 */
	protected $oConfig = null;

	protected function __construct( \stdClass $oStdClass, EntityConfig $oConfig, EntityFactory $entityFactory = null ) {
		if( !$entityFactory ) {
			$entityFactory = MediaWikiServices::getInstance()->getService(
				'BSEntityFactory'
			);
		}

		$this->entityFactory = $entityFactory;
		$this->oConfig = $oConfig;
		if( !empty( $oStdClass->{static::ATTR_ID} ) ) {
			$this->attributes[static::ATTR_ID] =
				(int) $oStdClass->{static::ATTR_ID};
		}
		if( !empty( $oStdClass->{static::ATTR_TYPE} ) ) {
			$this->attributes[static::ATTR_TYPE]
				= $oStdClass->{static::ATTR_TYPE};
		} else {
			$this->attributes[static::ATTR_TYPE] = static::TYPE;
		}
		if( !empty( $oStdClass->{static::ATTR_ARCHIVED} ) ) {
			$this->attributes[static::ATTR_ARCHIVED] = true;
		}

		$this->setValuesByObject( $oStdClass );
		if( $this->exists() ) {
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
		//we currently just use the source titles timestamps
		if( $attrName == static::ATTR_TIMESTAMP_CREATED ) {
			return $this->getTimestampCreated()
				? $this->getTimestampCreated()
				: $default;
		}
		if( $attrName == static::ATTR_TIMESTAMP_TOUCHED ) {
			return $this->getTimestampTouched()
				? $this->getTimestampTouched()
				: $default;
		}

		if( !isset( $this->attributes[$attrName] ) ) {
			return $default;
		}
		return $this->attributes[$attrName];
	}

	/**
	 * Returns the User object of the entity's owner
	 * @return \User
	 */
	public function getOwner() {
		return \User::newFromId( $this->get( static::ATTR_OWNER_ID, 0 ) );
	}

	/**
	 * Returns the entity store
	 * @param \IContextSource|null $context
	 * @return \BlueSpice\Data\IStore
	 * @throws \MWException
	 */
	protected function getStore( \IContextSource $context = null ) {
		if( !$context ) {
			$context = \RequestContext::getMain();
		}
		$sStoreClass = $this->getConfig()->get( 'StoreClass' );
		if( !class_exists( $sStoreClass ) ) {
			throw new \MWException( "Store class '$sStoreClass' not found" );
		}
		return new $sStoreClass( $context );
	}

	/**
	 * Sets an entity's attributes
	 * @param string $attrName
	 * @param mixed $value
	 * @return Entity
	 */
	public function set( $attrName, $value ) {
		//An Entity's id should never be changed
		if( $attrName == static::ATTR_ID ) {
			throw new \MWException( "An Entity's id can not be changed!" );
		}
		//An Entity's type should never be changed
		if( $attrName == static::ATTR_TYPE ) {
			throw new \MWException( "An Entity's type can not be changed!" );
		}

		$this->attributes[$attrName] = $value;
		return $this->setUnsavedChanges();
	}

	/**
	 * Returns the instance - Should not be used directly. This is a workaround
	 * as all entity __construc methods are protected. Use mediawiki service
	 * 'BSEntityFactory' instead
	 * @param \stdClass $data
	 * @param \BlueSpice\EntityConfig $oConfig
	 * @param \BlueSpice\EntityFactory $entityFactory
	 * @return \static
	 */
	public static function newFromFactory( \stdClass $data, EntityConfig $oConfig, EntityFactory $entityFactory ) {
		return new static( $data, $oConfig );
	}

	/**
	 * Get Entity by EntityContent Object, wrapper for newFromObject
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('BSEntityFactory')->newFromContent() instead
	 * @param EntityContent $sContent
	 * @return Entity
	 */
	public static function newFromContent( EntityContent $sContent ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'BSEntityFactory'
		);
		return $entityFactory->newFromContent( $sContent );
	}

	/**
	 * Get Entity by Json Object
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('BSEntityFactory')->newFromObject() instead
	 * @param Object $oObject
	 * @return Entity
	 */
	public static function newFromObject( $oObject ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'BSEntityFactory'
		);
		return $entityFactory->newFromObject( $oObject );
	}

	/**
	 * Gets the related config object
	 * @return EntityConfig
	 */
	public function getConfig() {
		return $this->oConfig;
	}

	/**
	 * Gets the related Title object by ID
	 * @return \Title
	 */
	public static function getTitleFor( $iID ) {
		return \Title::makeTitle( static::NS, $iID );
	}

	/**
	 * Gets the source Title object
	 * @return Title
	 */
	public function getTitle() {
		return static::getTitleFor( $this->get( static::ATTR_ID, 0 ) );
	}

	/**
	* Get the last touched timestamp
	* @return string|boolean Last-touched timestamp, false if entity was not saved yet
	*/
	public function getTimestampTouched() {
		if( !$this->exists() ) {
			return false;
		}
		return $this->getTitle()->getTouched();
	}

	/**
	* Get the oldest revision timestamp of this entity
	* @return string|boolean Created timestamp, false if entity was not saved yet
	*/
	public function getTimestampCreated() {
		if( !$this->exists() ) {
			return false;
		}
		return $this->getTitle()->getEarliestRevTime();
	}

	/**
	 * Get Entity from ID, wrapper for newFromTitle
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('BSEntityFactory')->newFromID() instead
	 * @param int $iID
	 * @param boolean $bForceReload
	 * @return Entity | null
	 */
	public static function newFromID( $iID, $bForceReload = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'BSEntityFactory'
		);
		return $entityFactory->newFromID( $iID, static::NS, $bForceReload );
	}

	/**
	 * Main method for getting a Entity from a Title
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('BSEntityFactory')->newFromSourceTitle() instead
	 * @param \Title $oTitle
	 * @param boolean $bForceReload
	 * @return Entity
	 */
	public static function newFromTitle( \Title $oTitle, $bForceReload = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'BSEntityFactory'
		);
		return $entityFactory->newFromSourceTitle( $oTitle, $bForceReload );
	}

	/**
	 * Saves the current Entity
	 * @return \Status
	 */
	public function save( \User $oUser = null, $aOptions = [] ) {
		if( !$oUser instanceof \User ) {
			return \Status::newFatal( 'No User' );
		}
		if( $this->exists() && !$this->hasUnsavedChanges() ) {
			return \Status::newGood( $this );
		}
		$sContentClass = $this->getConfig()->get( 'ContentClass' );
		if( !class_exists( $sContentClass ) ) {
			return \Status::newFatal( "Content class '$sContentClass' not found" );
		}
		if( empty( $this->getID() ) ) {
			$this->attributes[static::ATTR_ID] = $sContentClass::generateID(
				$this
			);
		}
		if( empty( $this->getID() ) ) {
			return \Status::newFatal( 'No ID generated' );
		}
		if( empty($this->get( static::ATTR_OWNER_ID, 0 )) ) {
			$this->set( static::ATTR_OWNER_ID, (int) $oUser->getId() );
		}
		$sType = $this->getType();
		if( empty($sType) ) {
			return \Status::newFatal( 'Related Type error' );
		}

		$oTitle = $this->getTitle();
		if ( is_null( $oTitle ) ) {
			return \Status::newFatal( 'Related Title error' );
		}
		$sStoreClass = $this->getConfig()->get( 'StoreClass' );
		if( !class_exists( $sStoreClass ) ) {
			return \Status::newFatal( "Store class '$sStoreClass' not found" );
		}

		$schema = $this->getStore()->getWriter()->getSchema();
		$aData = array_intersect_key(
			$this->getFullData(),
			array_flip( $schema->getStorableFields() )
		);

		$oWikiPage = \WikiPage::factory( $oTitle );

		try {
			$oStatus = $oWikiPage->doEditContent(
				new $sContentClass( json_encode( $aData ) ),
				"",
				0,
				0,
				$oUser,
				null
			);
		} catch( \Exception $e ) {
			//Something probalby breaks json
			return \Status::newFatal( $e->getMessage() );
		}
		//TODO: check why this is not good
		if ( !$oStatus->isOK() ) {
			return $oStatus;
		}

		$this->setUnsavedChanges( false );

		\Hooks::run( 'BSEntitySaveComplete', [ $this, $oStatus, $oUser ] );
		$this->invalidateCache();
		return $oStatus;
	}

	/**
	 * Archives the current Entity
	 * @param \User|null $oUser
	 * @return \Status
	 */
	public function delete( \User $oUser = null ) {
		$status = \Status::newGood();

		\Hooks::run( 'BSEntityDelete', [ $this, $status, $oUser ] );
		if( !$status->isOK() ) {
			return $status;
		}
		$this->set( static::ATTR_ARCHIVED, true );
		$this->setUnsavedChanges();

		try {
			$oStatus = $this->save( $oUser );
		} catch( \Exception $e ) {
			return \Status::newFatal( $e->getMessage() );
		}

		\Hooks::run( 'BSEntityDeleteComplete', [ $this, $oStatus, $oUser ] );
		if( !$oStatus->isOK() ) {
			return $oStatus;
		}

		$this->invalidateCache();
		return $oStatus;
	}

	/**
	 * Restores the current Entity from archived state
	 * @param \User|null $user
	 * @return \Status
	 */
	public function undelete( \User $user = null ) {
		$status = \Status::newGood();

		\Hooks::run( 'BSEntityUndelete', [ $this, $status, $user ] );
		if( !$status->isOK() ) {
			return $status;
		}
		$this->set( static::ATTR_ARCHIVED, false );
		$this->setUnsavedChanges();

		try {
			$status = $this->save( $user );
		} catch( \Exception $e ) {
			return \Status::newFatal( $e->getMessage() );
		}

		\Hooks::run( 'BSEntityUndeleteComplete', [ $this, $status, $user ] );
		if( !$status->isOK() ) {
			return $status;
		}

		$this->invalidateCache();
		return $status;
	}

	/**
	 * Gets the Entity attributes formated for the api
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
				static::ATTR_TIMESTAMP_CREATED => $this->getTimestampCreated(),
				static::ATTR_TIMESTAMP_TOUCHED => $this->getTimestampTouched(),
			]
		);
		\Hooks::run( 'BSEntityGetFullData', [
			$this,
			&$data
		]);
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
	 * @param \IContextSource|null $context
	 * @return Renderer
	 */
	public function getRenderer( \IContextSource $context = null ) {
		if( !$context ) {
			$context = \RequestContext::getMain();
		}
		return Services::getInstance()->getBSRendererFactory()->get(
			$this->getConfig()->get( 'Renderer' ),
			$this->makeRendererParams( [Renderer::PARAM_CONTEXT => $context] )
		);
	}

	/**
	 * Checks, if the current Entity exists in the Wiki
	 * @return boolean
	 */
	public function exists() {
		$bExists = $this->getID() > 0 ? true : false;
		if ( !$bExists ) {
			return false;
		}
		$oTitle = $this->getTitle();
		if ( is_null( $oTitle ) ) {
			return false;
		}
		return $oTitle->exists();
	}

	/**
	 * Checks if this entity is marked as archived
	 * @return boolean
	 */
	public function isArchived() {
		return $this->get( static::ATTR_ARCHIVED, false ) !== false;
	}

	/**
	 * Checks if there are unsaved changes
	 * @return boolean
	 */
	public function hasUnsavedChanges() {
		return (bool) $this->bUnsavedChanges;
	}

	/**
	 * Returns the current id for the Entity
	 * @deprecated since version 3.0.0 - use get( $attrName ) instead
	 * @return int
	 */
	public function getID() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return (int) $this->get( static::ATTR_ID, 0 );
	}

	/**
	 * Returns the current user id for the Entity
	 * @deprecated since version 3.0.0 - use get( $attrName ) instead
	 * @return int
	 */
	public function getOwnerID() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return (int) $this->get( static::ATTR_OWNER_ID, 0 );
	}

	/**
	 * Returns the current type for the BSSocialEntity
	 * @deprecated since version 3.0.0 - use get( $attrName ) instead
	 * @return String
	 */
	public function getType() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->get( static::ATTR_TYPE );
	}

	/**
	 * Sets the current Entity to an unsaved changes mode, refreshes cache
	 * @param String $bStatus
	 * @return Entity
	 */
	public function setUnsavedChanges( $bStatus = true ) {
		$this->bUnsavedChanges = (bool) $bStatus;
		return $this;
	}

	/**
	 * Sets the current user ID
	 * @deprecated since version 3.0.0 - use set( $attrName, $value ) instead
	 * @param int
	 * @return Entity
	 */
	public function setOwnerID( $iOwnerID ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->set( static::ATTR_OWNER_ID, (int) $iOwnerID );
	}

	/**
	 * Returns a json serializeable stdClass
	 * @return stdClass
	 */
	public function jsonSerialize() {
		return (object) $this->getFullData();
	}

	/**
	 * @param \stdClass $data
	 */
	public function setValuesByObject( \stdClass $data ) {
		if( !empty( $data->{static::ATTR_ARCHIVED} ) ) {
			$this->set( static::ATTR_ARCHIVED, $data->{static::ATTR_ARCHIVED} );
		}
		if( !empty( $data->{static::ATTR_OWNER_ID} ) ) {
			$this->set( static::ATTR_OWNER_ID, $data->{static::ATTR_OWNER_ID} );
		}

		\Hooks::run('BSEntitySetValuesByObject', [
			$this,
			$data
		]);
	}

	/**
	 * Checks if the given User is the owner of this entity
	 * @param \User $oUser
	 * @return boolean
	 */
	public function userIsOwner( \User $oUser ) {
		if( $oUser->isAnon() || $this->get( static::ATTR_OWNER_ID, 0 ) < 1) {
			return false;
		}
		return $oUser->getId() == $this->get( static::ATTR_OWNER_ID, 0 );
	}

	/**
	 * Invalidated the cache
	 * @return Entity
	 */
	public function invalidateCache() {
		$this->invalidateTitleCache( wfTimestampNow() );
		$this->entityFactory->detachCache( $this );
		\Hooks::run( 'BSEntityInvalidate', [ $this ] );
		return $this;
	}

	/**
	 * Almost a copy of Title::invalidateCache method - but we need an immediate
	 * invalidation, not whenever the db feels 'idle'
	 * Updates page_touched for this page; called from LinksUpdate.php
	 *
	 * @param string|null $purgeTime [optional] TS_MW timestamp
	 * @return bool True if the update succeeded
	 */
	protected function invalidateTitleCache( $purgeTime = null ) {
		if ( wfReadOnly() ) {
			return false;
		}

		if( !$this->getTitle()->exists() ) {
			return true; // avoid gap locking if we know it's not there
		}

		$method = __METHOD__;
		$dbw = wfGetDB( DB_MASTER );
		$conds = $this->getTitle()->pageCond();

		$dbTimestamp = $dbw->timestamp( $purgeTime ?: time() );

		$dbw->update(
			'page',
			[ 'page_touched' => $dbTimestamp ],
			$conds + [ 'page_touched < ' . $dbw->addQuotes( $dbTimestamp ) ],
			$method
		);

		return true;
	}
}
