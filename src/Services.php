<?php

namespace BlueSpice;

class Services extends ServicesDecorator {

	/**
	 *
	 * @return ExtensionRegistry
	 */
	public function getBSExtensionRegistry() {
		return $this->decoratedServices->getService( 'BSExtensionRegistry' );
	}

	/**
	 *
	 * @return ExtensionFactory
	 */
	public function getBSExtensionFactory() {
		return $this->decoratedServices->getService( 'BSExtensionFactory' );
	}

	/**
	 *
	 * @return ConfigDefinitionFactory
	 */
	public function getBSConfigDefinitionFactory() {
		return $this->decoratedServices->getService( 'BSConfigDefinitionFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\Factory
	 */
	public function getBSDynamicFileDispatcherFactory() {
		return $this->decoratedServices->getService( 'BSDynamicFileDispatcherFactory' );
	}

	/**
	 *
	 * @return DynamicFileDispatcher\UrlBuilder
	 */
	public function getBSDynamicFileDispatcherUrlBuilder() {
		return $this->decoratedServices->getService( 'BSDynamicFileDispatcherUrlBuilder' );
	}

	/**
	 *
	 * @return EntityRegistry
	 */
	public function getBSEntityRegistry() {
		return $this->decoratedServices->getService( 'BSEntityRegistry' );
	}

	/**
	 *
	 * @return EntityConfigFactory
	 */
	public function getBSEntityConfigFactory() {
		return $this->decoratedServices->getService( 'BSEntityConfigFactory' );
	}

	/**
	 *
	 * @return EntityFactory
	 */
	public function getBSEntityFactory() {
		return $this->decoratedServices->getService( 'BSEntityFactory' );
	}

	/**
	 *
	 * @return AdminToolFactory
	 */
	public function getBSAdminToolFactory() {
		return $this->decoratedServices->getService( 'BSAdminToolFactory' );
	}

	/**
	 *
	 * @return PageToolFactory
	 */
	public function getBSPageToolFactory() {
		return $this->decoratedServices->getService( 'BSPageToolFactory' );
	}

	/**
	 *
	 * @return TagFactory
	 */
	public function getBSTagFactory() {
		return $this->decoratedServices->getService( 'BSTagFactory' );
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
}
