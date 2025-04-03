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
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use BlueSpice\Content\Entity as EntityContent;
use BlueSpice\Data\Entity\IStore;
use MediaWiki\Config\Config;
use MediaWiki\Content\TextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class EntityFactory {
	protected $storedById = [];
	protected $legacyTypeCache = [];

	/**
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $entityRegistry = null;

	/**
	 * @var EntityConfigFactory
	 */
	protected $configFactory = null;

	/**
	 * @var Config
	 */
	protected $config = null;

	/**
	 * @param ExtensionAttributeBasedRegistry $entityRegistry
	 * @param EntityConfigFactory $configFactory
	 * @param Config $config
	 */
	public function __construct( ExtensionAttributeBasedRegistry $entityRegistry,
		EntityConfigFactory $configFactory, Config $config ) {
		$this->entityRegistry = $entityRegistry;
		$this->configFactory = $configFactory;
		$this->config = $config;
	}

	/**
	 * @param string $type
	 * @param \stdClass $data
	 * @param EntityConfig|null $entityConfig
	 * @param IStore|null $store
	 * @return Entity
	 * @throws MWException
	 */
	protected function factory( $type, $data, ?EntityConfig $entityConfig = null,
		?IStore $store = null ) {
		if ( !$entityConfig ) {
			$entityConfig = $this->makeConfig( $type );
		}
		if ( !$store ) {
			$store = $this->makeStore( $type, $entityConfig );
			if ( !$store instanceof IStore ) {
				// TODO: Return a DummyEntity instead of null.
				return null;
			}
		}
		$entityClass = $entityConfig->get( 'EntityClass' );
		return $entityClass::newFromFactory( $data, $entityConfig, $store, $this );
	}

	/**
	 * Get ContentEntity by EntityContent Object, wrapper for newFromObject
	 *
	 * @param EntityContent $sContent
	 * @return Entity|null
	 */
	public function newFromContent( EntityContent $sContent ) {
		$aContent = $sContent->getJsonData();
		if ( empty( $aContent ) ) {
			return null;
		}

		return $this->newFromObject( (object)$aContent );
	}

	/**
	 * Get Entity by Json Object
	 *
	 * @param stdClass|null $object
	 * @return Entity|null
	 */
	public function newFromObject( ?\stdClass $object = null ) {
		if ( !$object ) {
			return null;
		}

		if ( empty( $object->type ) ) {
			return null;
		}

		if ( !$this->entityRegistry->getValue( $object->type ) ) {
			return null;
		}

		if ( !empty( $object->id ) ) {
			return $this->newFromID( $object->id, $object->type );
		}

		$instance = $this->factory(
			$object->type,
			$object
		);
		if ( !$instance instanceof Entity ) {
			return null;
		}
		return $this->appendCache( $instance );
	}

	/**
	 * Get Entity from ID
	 *
	 * @param mixed $id
	 * @param string|int $type - int namespace is used for legecy content entities
	 * @param bool $reload
	 * @return Entity|null
	 */
	public function newFromID( $id, $type, $reload = false ) {
		if ( empty( $id ) ) {
			return null;
		}
		$instance = null;
		if ( is_numeric( $type ) ) {
			$type = $this->makeTypeFromLegacyContentEntity( $id, $type );
		}

		if ( !$type ) {
			return null;
		}
		if ( !$reload ) {
			$instance = $this->getInstanceFromCacheByID( $id, $type );
		}
		if ( $instance ) {
			return $instance;
		}

		$config = $this->makeConfig( $type );
		$store = $this->makeStore( $type, $config );
		if ( !$store ) {
			return null;
		}

		$data = $store->getReader()->resolveNativeDataFromID(
			$id,
			$config
		);

		if ( !$data ) {
			return null;
		}

		$instance = $this->factory(
			$data->type,
			$data,
			$config,
			$store
		);
		if ( !$instance instanceof Entity ) {
			return null;
		}
		return $this->appendCache( $instance );
	}

	/**
	 * @param string $type
	 * @return EntityConfig
	 */
	protected function makeConfig( $type ) {
		$entityConfig = $this->configFactory->newFromType( $type );
		if ( !$entityConfig instanceof EntityConfig ) {
			return null;
		}
		return $entityConfig;
	}

	/**
	 * @param string $type
	 * @param EntityConfig|null $entityConfig
	 * @return IStore|null
	 */
	protected function makeStore( $type, ?EntityConfig $entityConfig = null ) {
		if ( !$entityConfig ) {
			$entityConfig = $this->makeConfig( $type );
		}
		if ( !$entityConfig ) {
			return null;
		}
		$storeClass = $entityConfig->get( 'StoreClass' );
		if ( !class_exists( $storeClass ) ) {
			return null;
		}
		$store = new $storeClass();
		return $store;
	}

	/**
	 * Main method for getting a ContentEntity from a Title
	 *
	 * @param Title|null $title
	 * @param bool $reload
	 * @return Entity|null
	 */
	public function newFromSourceTitle( ?Title $title = null, $reload = false ) {
		if ( !$title ) {
			return null;
		}
		$id = (int)$title->getText();

		return $this->newFromID( $id, $title->getNamespace(), $reload );
	}

	/**
	 * Adds a Entity to the cache
	 *
	 * @param Entity &$oInstance
	 * @return Entity
	 */
	protected function appendCache( Entity &$oInstance ) {
		if ( $this->hasCacheEntry( $oInstance->get( Entity::ATTR_ID ),
			$oInstance->get( Entity::TYPE ) ) ) {
			return $oInstance;
		}
		$this->storedById[$oInstance->get( Entity::TYPE )][$oInstance->get( Entity::ATTR_ID )] = $oInstance;
		return $oInstance;
	}

	/**
	 * Removes a Entity from the cache if it's in
	 *
	 * @param Entity &$oInstance
	 * @return Entity
	 */
	public function detachCache( Entity &$oInstance ) {
		if ( !$this->hasCacheEntry( $oInstance->get( Entity::ATTR_ID ),
			$oInstance->get( Entity::TYPE ) ) ) {
			return $oInstance;
		}
		unset( $this->storedById[$oInstance->get( Entity::TYPE )][$oInstance->get( Entity::ATTR_ID )] );
		return $oInstance;
	}

	/**
	 * Gets a instance of the Entity from the cache by ID
	 *
	 * @param mixed $id
	 * @param string $type
	 * @return Entity
	 */
	protected function getInstanceFromCacheByID( $id, $type ) {
		if ( !$this->hasCacheEntry( $id, $type ) ) {
			return null;
		}
		return $this->storedById[$type][$id];
	}

	/**
	 * @param mixed $id
	 * @param string $type
	 * @return bool
	 */
	protected function hasCacheEntry( $id, $type ) {
		return isset( $this->storedById[$type][$id] );
	}

	/**
	 * @param mixed $id
	 * @param int $type
	 * @return string|bool
	 */
	protected function makeTypeFromLegacyContentEntity( $id, $type ) {
		if ( isset( $this->legacyTypeCache[$type][$id] ) ) {
			return $this->legacyTypeCache[$type][$id];
		}
		$title = Title::makeTitle( $type, $id );
		if ( !$title || !$title->exists() ) {
			return false;
		}

		$content = MediaWikiServices::getInstance()->getWikiPageFactory()->newFromTitle( $title )->getContent();
		if ( !$content ) {
			return false;
		}
		$text = ( $content instanceof TextContent ) ? $content->getText() : '';
		$content = new EntityContent( $text );
		$data = (object)$content->getData()->getValue();

		if ( empty( $data->type ) ) {
			return false;
		}
		if ( !$this->entityRegistry->getValue( $data->type ) ) {
			return false;
		}
		$this->legacyTypeCache[$type][$id] = $data->type;
		return $data->type;
	}
}
