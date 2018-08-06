<?php
/**
 * EntityFactory class for BlueSpice
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

class EntityFactory {
	protected $storedById = array();

	/**
	 *
	 * @var \BlueSpice\EntityRegistry
	 */
	protected $entityRegistry = null;

	/**
	 *
	 * @var \BlueSpice\EntityConfigFactory
	 */
	protected $configFactory = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 * @param \BlueSpice\EntityRegistry $entityRegistry
	 * @param \BlueSpice\EntityConfigFactory $configFactory
	 * @param \Config $config
	 * @return Entity | null
	 */
	public function __construct( $entityRegistry, $configFactory, $config ) {
		$this->entityRegistry = $entityRegistry;
		$this->configFactory = $configFactory;
		$this->config = $config;
	}

	protected function factory( $type, $data ) {
		$entityConfig = $this->configFactory->newFromType( $type );
		if( !$entityConfig instanceof EntityConfig ) {
			//TODO: Return a DummyEntity instead of null.
			return null;
		}

		$entityClass = $entityConfig->get( 'EntityClass' );
		return $entityClass::newFromFactory( $data, $entityConfig, $this );
	}

	/**
	 * Get Entity by EntityContent Object, wrapper for newFromObject
	 * @param EntityContent $sContent
	 * @return Entity | null
	 */
	public function newFromContent( EntityContent $sContent ) {
		$aContent = $sContent->getJsonData();
		if ( empty($aContent) ) {
			return null;
		}

		return $this->newFromObject( (object) $aContent );
	}

	/**
	 * Get Entity by Json Object
	 * @param Object|null $object
	 * @return Entity | null
	 */
	public function newFromObject( \stdClass $object = null ) {
		if( !$object ) {
			return null;
		}

		if( empty( $object->type ) ) {
			return null;
		}

		if( !$this->entityRegistry->hasType( $object->type ) ) {
			return null;
		}

		if( !empty($object->id) && (int) $object->id !== 0 ) {
			$entityConfig = $this->configFactory->newFromType( $object->type );
			if( !$entityConfig instanceof EntityConfig ) {
				//TODO: Return a DummyEntity instead of null.
				return null;
			}
			$entityClass = $entityConfig->get( 'EntityClass' );
			return $this->newFromID( $object->id, $entityClass::NS );
		}

		$instance = $this->factory(
			$object->type,
			$object
		);
		if( !$instance instanceof Entity ) {
			return null;
		}
		return $this->appendCache( $instance );
	}

	/**
	 * Get Entity from ID
	 * @param int $id
	 * @param int $ns
	 * @param boolean $reload
	 * @return Entity | null
	 */
	public function newFromID( $id, $ns, $reload = false ) {
		if ( !is_numeric( $id ) || !is_numeric( $ns ) ) {
			return null;
		}
		$id = (int) $id;
		$ns = (int) $ns;

		$instance = null;
		if( !$reload ) {
			$instance = $this->getInstanceFromCacheByID( $id, $ns );
		}
		if( $instance ) {
			return $instance;
		}

		$title = \Title::makeTitle( $ns, $id );

		if( !$title || !$title->exists() ) {
			return null;
		}

		$sText = \BsPageContentProvider::getInstance()->getContentFromTitle(
			$title
		);

		$content = new EntityContent( $sText );
		$data = (object) $content->getData()->getValue();

		if( empty($data->type) ) {
			return null;
		}

		if( !$this->entityRegistry->hasType( $data->type ) ) {
			return null;
		}

		$instance = $this->factory(
			$data->type,
			$data
		);
		if( !$instance instanceof Entity ) {
			return null;
		}
		return $this->appendCache( $instance );
	}

	/**
	 * Main method for getting a Entity from a Title
	 * @param \Title|null $title
	 * @param boolean $reload
	 * @return Entity | null
	 */
	public function newFromSourceTitle( \Title $title = null, $reload = false ) {
		if ( !$title ) {
			return null;
		}
		$id = (int) $title->getText();

		return $this->newFromID( $id, $title->getNamespace(), $reload );
	}

	/**
	 * Adds a Entity to the cache
	 * @param Entity $oInstance
	 * @return Entity
	 */
	protected function appendCache( Entity &$oInstance ) {
		if( $this->hasCacheEntry( $oInstance->getID(), $oInstance::NS ) ) {
			return $oInstance;
		}
		$this->storedById[$oInstance::NS][$oInstance->getID()] = $oInstance;
		return $oInstance;
	}

	/**
	 * Removes a Entity from the cache if it's in
	 * @param Entity $oInstance
	 * @return Entity
	 */
	public function detachCache( Entity &$oInstance ) {
		if( !$this->hasCacheEntry($oInstance->getID(), $oInstance::NS ) ) {
			return $oInstance;
		}
		unset( $this->storedById[$oInstance::NS][$oInstance->getID()] );
		return $oInstance;
	}

	/**
	 * Gets a instance of the Entity from the cache by ID
	 * @param int $id
	 * @return Entity
	 */
	protected function getInstanceFromCacheByID( $id, $ns = -1 ) {
		if( !$this->hasCacheEntry( $id, $ns ) ) {
			return null;
		}
		return $this->storedById[$ns][(int) $id];
	}

	protected function hasCacheEntry( $id, $ns = -1 ) {
		return isset( $this->storedById[$ns][(int) $id] );
	}
}
