<?php

namespace BlueSpice;

use MediaWiki\MediaWikiServices;

class Services extends MediaWikiServices {

	/**
	 *
	 * @return ExtensionRegistry
	 */
	public function getBSExtensionRegistry() {
		return $this->getService( 'BSExtensionRegistry' );
	}

	/**
	 *
	 * @return ExtensionFactory
	 */
	public function getBSExtensionFactory() {
		return $this->getService( 'BSExtensionFactory' );
	}

	/**
	 *
	 * @return ConfigDefinitionFactory
	 */
	public function getBSConfigDefinitionFactory() {
		return $this->getService( 'BSConfigDefinitionFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\Factory
	 */
	public function getBSDynamicFileDispatcherFactory() {
		return $this->getService( 'BSDynamicFileDispatcherFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\UrlBuilder
	 */
	public function getBSDynamicFileDispatcherUrlBuilder() {
		return $this->getService( 'BSDynamicFileDispatcherUrlBuilder' );
	}

	/**
	 *
	 * @return EntityRegistry
	 */
	public function getBSEntityRegistry() {
		return $this->getService( 'BSEntityRegistry' );
	}

	/**
	 *
	 * @return EntityConfigFactory
	 */
	public function getBSEntityConfigFactory() {
		return $this->getService( 'BSEntityConfigFactory' );
	}

	/**
	 *
	 * @return EntityFactory
	 */
	public function getBSEntityFactory() {
		return $this->getService( 'BSEntityFactory' );
	}
}