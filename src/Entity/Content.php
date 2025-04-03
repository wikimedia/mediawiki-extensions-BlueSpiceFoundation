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
namespace BlueSpice\Entity;

use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

abstract class Content extends \BlueSpice\Entity {
	public const NS = -1;

	/**
	 *
	 * @var string
	 */
	private $tsCreatedCache = null;

	/**
	 *
	 * @var string
	 */
	private $tsTouchedCache = null;

	/**
	 * Returns an entity's attributes or the given default, if not set
	 * @param string $attrName
	 * @param mixed|null $default
	 * @return mixed
	 */
	public function get( $attrName, $default = null ) {
		// we currently just use the source titles timestamps
		if ( $attrName == static::ATTR_TIMESTAMP_CREATED ) {
			return $this->getTimestampCreated()
				? $this->getTimestampCreated()
				: $default;
		}
		if ( $attrName == static::ATTR_TIMESTAMP_TOUCHED ) {
			return $this->getTimestampTouched()
				? $this->getTimestampTouched()
				: $default;
		}

		return parent::get( $attrName, $default );
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
	 * Gets the source Title object
	 * @return Title
	 */
	public function getTitle() {
		return Title::makeTitle( static::NS, $this->get( static::ATTR_ID, 0 ) );
	}

	/**
	 * Get the last touched timestamp
	 * @return string|bool Last-touched timestamp, false if entity was not saved yet
	 */
	public function getTimestampTouched() {
		if ( !$this->exists() ) {
			return false;
		}
		if ( $this->tsTouchedCache ) {
			return $this->tsTouchedCache;
		}
		$this->tsTouchedCache = $this->getTitle()->getTouched();
		return $this->tsTouchedCache;
	}

	/**
	 * Get the oldest revision timestamp of this entity
	 * @return string|bool Created timestamp, false if entity was not saved yet
	 */
	public function getTimestampCreated() {
		if ( !$this->exists() ) {
			return false;
		}
		if ( $this->tsCreatedCache ) {
			return $this->tsCreatedCache;
		}
		$firstRev = $this->services->getRevisionLookup()
			->getFirstRevision( $this->getTitle()->toPageIdentity() );
		$this->tsCreatedCache = $firstRev ? $firstRev->getTimestamp() : false;
		return $this->tsCreatedCache;
	}

	/**
	 * Saves the current Entity
	 * @param User|null $oUser
	 * @param array $aOptions
	 * @return Status
	 */
	public function save( ?User $oUser = null, $aOptions = [] ) {
		$oTitle = $this->getTitle();
		if ( $oTitle === null ) {
			return Status::newFatal( 'Related Title error' );
		}

		return parent::save( $oUser, $aOptions );
	}

	/**
	 * Gets the Entity attributes formated for the api
	 * @param array $data
	 * @return array
	 */
	public function getFullData( $data = [] ) {
		return parent::getFullData( array_merge(
			$data,
			[
				static::ATTR_TIMESTAMP_CREATED => $this->getTimestampCreated(),
				static::ATTR_TIMESTAMP_TOUCHED => $this->getTimestampTouched(),
			]
		) );
	}

	/**
	 * Checks, if the current Entity exists in the Wiki
	 * @return bool
	 */
	public function exists() {
		if ( !parent::exists() ) {
			return false;
		}
		$oTitle = $this->getTitle();
		if ( $oTitle === null ) {
			return false;
		}
		return $oTitle->exists();
	}

	/**
	 * Invalidated the cache
	 * @return Entity
	 */
	public function invalidateCache() {
		$this->invalidateTitleCache( wfTimestampNow() );
		$this->tsCreatedCache = null;
		$this->tsTouchedCache = null;
		return parent::invalidateCache();
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
		if ( $this->services->getReadOnlyMode()->isReadOnly() ) {
			return false;
		}

		if ( !$this->getTitle()->exists() ) {
			// avoid gap locking if we know it's not there
			return true;
		}

		$dbw = $this->services->getDBLoadBalancer()->getConnection( DB_PRIMARY );
		$conds = $this->getTitle()->pageCond();

		$dbTimestamp = $dbw->timestamp( $purgeTime ?: time() );

		$dbw->update(
			'page',
			[ 'page_touched' => $dbTimestamp ],
			$conds + [ 'page_touched < ' . $dbw->addQuotes( $dbTimestamp ) ],
			__METHOD__
		);

		return true;
	}
}
