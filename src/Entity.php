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

abstract class Entity implements \JsonSerializable {
	const NS = -1;

	const ATTR_TYPE = 'type';
	const ATTR_ID = 'id';
	const ATTR_OWNER_ID = 'ownerid';
	const ATTR_ARCHIVED = 'archived';
	const ATTR_PARENT_ID = 'parentid';
	const ATTR_TIMESTAMP_CREATED = 'timestampcreated';
	const ATTR_TIMESTAMP_TOUCHED = 'timestamptouched';

	/**
	 *
	 * @var EntityFactory
	 */
	protected $entityFactory = null;
	protected $bUnsavedChanges = true;

	/**
	 * @var EntityConfig
	 */
	protected $oConfig = null;

	protected $iID = 0;
	protected $iOwnerID = 0;
	protected $sType = '';
	protected $bArchived = false;

	protected function __construct( \stdClass $oStdClass, EntityConfig $oConfig, EntityFactory $entityFactory = null ) {
		if( !$entityFactory ) {
			$entityFactory = MediaWikiServices::getInstance()->getService(
				'EntityFactory'
			);
		}

		$this->entityFactory = $entityFactory;
		$this->oConfig = $oConfig;
		if( !empty( $oStdClass->{static::ATTR_ID} ) ) {
			$this->iID = (int) $oStdClass->{static::ATTR_ID};
		}
		if( !empty( $oStdClass->{static::ATTR_TYPE} ) ) {
			$this->sType = $oStdClass->{static::ATTR_TYPE};
		}
		if( !empty( $oStdClass->{static::ATTR_ARCHIVED} ) ) {
			$this->bArchived = $oStdClass->{static::ATTR_ARCHIVED};
		}
		if( !empty( $oStdClass->{static::ATTR_OWNER_ID} ) ) {
			$this->iOwnerID = $oStdClass->{static::ATTR_OWNER_ID};
		}

		$this->setValuesByObject( $oStdClass );
		if( $this->exists() ) {
			$this->setUnsavedChanges( false );
		}
	}

	/**
	 * Returns the instance - Should not be used directly. This is a workaround
	 * as all entity __construc methods are private. Use mediawiki service
	 * 'EntityFactory' instead
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
	 * ('EntityFactory')->newFromContent() instead
	 * @param EntityContent $sContent
	 * @return Entity
	 */
	public static function newFromContent( EntityContent $sContent ) {
		wfDeprecated( __METHOD__, '3.0.0' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'EntityFactory'
		);
		return $entityFactory->newFromContent( $sContent );
	}

	/**
	 * Get Entity by Json Object
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('EntityFactory')->newFromObject() instead
	 * @param Object $oObject
	 * @return Entity
	 */
	public static function newFromObject( $oObject ) {
		wfDeprecated( __METHOD__, '3.0.0' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'EntityFactory'
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
	 * Gets the related Title object
	 * @return Title
	 */
	public function getTitle() {
		return static::getTitleFor( $this->iID );
	}

	public function getTimestampTouched() {
		return $this->getTitle()->getTouched();
	}

	public function getTimestampCreated() {
		return $this->getTitle()->getEarliestRevTime();
	}

	/**
	 * Get Entity from ID, wrapper for newFromTitle
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('EntityFactory')->newFromID() instead
	 * @param int $iID
	 * @param boolean $bForceReload
	 * @return Entity | null
	 */
	public static function newFromID( $iID, $bForceReload = false ) {
		wfDeprecated( __METHOD__, '3.0.0' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'EntityFactory'
		);
		return $entityFactory->newFromID( $iID, static::NS, $bForceReload );
	}

	/**
	 * Main method for getting a Entity from a Title
	 * @deprecated since version 3.0.0 - Use mediawiki service
	 * ('EntityFactory')->newFromSourceTitle() instead
	 * @param \Title $oTitle
	 * @param boolean $bForceReload
	 * @return Entity
	 */
	public static function newFromTitle( \Title $oTitle, $bForceReload = false ) {
		wfDeprecated( __METHOD__, '3.0.0' );
		$entityFactory = MediaWikiServices::getInstance()->getService(
			'EntityFactory'
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
			$this->iID = $sContentClass::generateID( $this );
		}
		if( empty( $this->getID() ) ) {
			return \Status::newFatal( 'No ID generated' );
		}
		if( empty($this->getOwnerID()) ) {
			$this->setOwnerID( (int) $oUser->getId() );
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
		$oStore = new $sStoreClass( \RequestContext::getMain() );
		$oSchema = $oStore->getWriter()->getSchema();
		$aData = array_intersect_key(
			$this->getFullData(),
			array_flip( $oSchema->getStorableFields() )
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
	 * @return \Status
	 */
	public function delete( \User $oUser = null, $aOptions = [] ) {
		$oTitle = $this->getTitle();

		$oStatus = null;
		$oWikiPage = \WikiPage::factory( $oTitle );

		\Hooks::run( 'BSEntityDelete', [ $this, $oStatus, $oUser ] );
		if( $oStatus instanceof \Status && $oStatus->isOK() ) {
			return $oStatus;
		}
		$this->bArchived = true;
		$this->setUnsavedChanges();

		try {
			$oStatus = $this->save();
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
	 * Gets the Entity attributes formated for the api
	 * @return array
	 */
	public function getFullData( $aData = array() ) {
		$aData = array_merge(
			$aData,
			array(
				'id' => $this->getID(),
				'ownerid' => $this->getOwnerID(),
				'type' => $this->getType(),
				'archived' => $this->isArchived(),
			)
		);
		\Hooks::run('BSEntityGetFullData', [
			$this,
			&$aData
		]);
		return $aData;
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
	 * @return boolean
	 */
	public function isArchived() {
		return $this->bArchived;
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
	 * @return int
	 */
	public function getID() {
		return (int) $this->iID;
	}

	/**
	 * Returns the current user id for the Entity
	 * @return int
	 */
	public function getOwnerID() {
		return (int) $this->iOwnerID;
	}

	/**
	 * Returns the current type for the BSSocialEntity
	 * @return String
	 */
	public function getType() {
		return $this->sType;
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
	 * @param int
	 * @return Entity
	 */
	public function setOwnerID( $iOwnerID ) {
		return $this->setUnsavedChanges(
			$this->iOwnerID = (int) $iOwnerID
		);
	}

	/**
	 * Subclass needs to return the current Entity as a Json encoded
	 * string
	 * @deprecated since 2.27.0 - Use json_encode( $oInstance ) instead
	 * @return stdObject - Subclass needs to return encoded string!
	 */
	public function toJson() {
		return json_encode( (object) static::getFullData() );
	}

	/**
	 * Returns a json serializeable stdClass
	 * @return stdClass
	 */
	public function jsonSerialize() {
		return (object) static::getFullData();
	}

	/**
	 * @param \stdClass $oData
	 */
	public function setValuesByObject( \stdClass $oData ) {
		\Hooks::run('BSEntitySetValuesByObject', [
			$this,
			$oData
		]);
	}

	/**
	 * Checks if the given User is the owner of this entity
	 * @param \User $oUser
	 * @return boolean
	 */
	public function userIsOwner( \User $oUser ) {
		if( $oUser->isAnon() || $this->getOwnerID() < 1) {
			return false;
		}
		return $oUser->getId() == $this->getOwnerID();
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
	 * @param string $purgeTime [optional] TS_MW timestamp
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
