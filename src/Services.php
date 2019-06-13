<?php

namespace BlueSpice;

class Services extends ServicesDecorator {

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

	/**
	 *
	 * @return AdminToolFactory
	 */
	public function getBSAdminToolFactory() {
		return $this->getService( 'BSAdminToolFactory' );
	}

	/**
	 *
	 * @return PageToolFactory
	 */
	public function getBSPageToolFactory() {
		return $this->getService( 'BSPageToolFactory' );
	}

	/**
	 *
	 * @return TagFactory
	 */
	public function getBSTagFactory() {
		return $this->getService( 'BSTagFactory' );
	}

	/**
	 * @return RendererFactory
	 */
	public function getBSRendererFactory() {
		return $this->getService( 'BSRendererFactory' );
	}

	/**
	 *
	 * @return SkinDataRendererFactory
	 */
	public function getBSSkinDataRendererFactory() {
		return $this->getService( 'BSSkinDataRendererFactory' );
	}

	/**
	 *
	 * @return SettingPathFactory
	 */
	public function getBSSettingPathFactory() {
		return $this->getService( 'BSSettingPathFactory' );
	}

	/**
	 *
	 * @return UtilityFactory
	 */
	public function getBSUtilityFactory() {
		return $this->getService( 'BSUtilityFactory' );
	}

	/**
	 *
	 * @return NotificationManager
	 */
	public function getBSNotificationManager() {
		return $this->getService( 'BSNotificationManager' );
	}

	/**
	 *
	 * @return TargetCacheFactory
	 */
	public function getBSTargetCacheFactory() {
		return $this->getService( 'BSTargetCacheFactory' );
	}

	/**
	 *
	 * @return TargetCache\Title
	 */
	public function getBSTargetCacheTitle() {
		return $this->getService( 'BSTargetCacheTitle' );
	}

}
