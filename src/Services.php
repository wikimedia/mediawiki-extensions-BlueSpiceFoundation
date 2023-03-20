<?php

namespace BlueSpice;

use MediaWiki\MediaWikiServices;

/**
 * DEPRECATED!
 * @deprecated since version 3.2.0 - use \MediaWiki\MediaWikiSerices
 */
class Services extends MediaWikiServices {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return ExtensionRegistry
	 */
	public function getBSExtensionRegistry() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSExtensionRegistry' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return ExtensionFactory
	 */
	public function getBSExtensionFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSExtensionFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return ConfigDefinitionFactory
	 */
	public function getBSConfigDefinitionFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSConfigDefinitionFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return DynamicFileDispatcher\Factory
	 */
	public function getBSDynamicFileDispatcherFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSDynamicFileDispatcherFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return DynamicFileDispatcher\UrlBuilder
	 */
	public function getBSDynamicFileDispatcherUrlBuilder() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSDynamicFileDispatcherUrlBuilder' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return EntityConfigFactory
	 */
	public function getBSEntityConfigFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSEntityConfigFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return EntityFactory
	 */
	public function getBSEntityFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSEntityFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return AdminToolFactory
	 */
	public function getBSAdminToolFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSAdminToolFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return TagFactory
	 */
	public function getBSTagFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSTagFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return RendererFactory
	 */
	public function getBSRendererFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSRendererFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return SkinDataRendererFactory
	 */
	public function getBSSkinDataRendererFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSSkinDataRendererFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return SettingPathFactory
	 */
	public function getBSSettingPathFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSSettingPathFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return TaskFactory
	 */
	public function getBSTaskFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSTaskFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return UtilityFactory
	 */
	public function getBSUtilityFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSUtilityFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return NotificationManager
	 */
	public function getBSNotificationManager() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSNotificationManager' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return TargetCacheFactory
	 */
	public function getBSTargetCacheFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSTargetCacheFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return TargetCache\Title
	 */
	public function getBSTargetCacheTitle() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSTargetCacheTitle' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return Permission\PermissionRegistry
	 */
	public function getBSPermissionRegistry() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSPermissionRegistry' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return PermissionLockdownFactory
	 */
	public function getBSPermissionLockdownFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSPermissionLockdownFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return \BlueSpice\Permission\Role\Manager
	 */
	public function getBSRoleManager() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSRoleManager' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return TemplateFactory
	 */
	public function getBSTemplateFactory() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSTemplateFactory' );
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use ->getService( 'ServiceName' )
	 * @return DeferredNotificationStack
	 */
	public function getBSDeferredNotificationStack() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->getService( 'BSDeferredNotificationStack' );
	}

}
