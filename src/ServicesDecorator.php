<?php

namespace BlueSpice;

use ActorMigration;
use CommentStore;
use Config;
use ConfigFactory;
use CryptHKDF;
use CryptRand;
use EventRelayerGroup;
use GenderCache;
use IBufferingStatsdDataFactory;
use Language;
use LinkCache;
use MediaHandlerFactory;
use MediaWiki\Http\HttpRequestFactory;
use MediaWiki\Interwiki\InterwikiLookup;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkRendererFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Preferences\PreferencesFactory;
use MediaWiki\Revision\RevisionFactory;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionStore;
use MediaWiki\Shell\CommandFactory;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Storage\BlobStore;
use MediaWiki\Storage\BlobStoreFactory;
use MediaWiki\Storage\NameTableStore;
use MimeAnalyzer;
use NamespaceInfo;
use Parser;
use ParserCache;
use ProxyLookup;
use SearchEngine;
use SearchEngineConfig;
use SearchEngineFactory;
use SiteLookup;
use SiteStore;
use SkinFactory;
use TitleFormatter;
use TitleParser;
use VirtualRESTServiceClient;
use WatchedItemQueryService;
use WatchedItemStoreInterface;
use Wikimedia\Rdbms\LBFactory;
use Wikimedia\Rdbms\LoadBalancer;
use Wikimedia\Services\ServiceContainer;

/**
 * DEPRECATED!
 * @deprecated since version 3.2.0 - use \MediaWiki\MediaWikiSerices
 */
class ServicesDecorator extends ServiceContainer {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.2.0 - use \MediaWiki\MediaWikiSerices::getInstance
	 * @return ServicesDecorator
	 */
	public static function getInstance() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return new static();
	}

	/**
	 * Return the service registered under $name
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getService( $name ) {
		return MediaWikiServices::getInstance()->getService( $name );
	}

	/**
	 * Returns true if a service is defined for $name, that is, if a call to getService( $name )
	 * would return a service instance.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasService( $name ) {
		return MediaWikiServices::getInstance()->hasService( $name );
	}

	 /**
	  * Returns true if the container can return an entry for the given identifier.
	  * Returns false otherwise.
	  *
	  * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	  * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	  *
	  * @param string $name Identifier of the entry to look for.
	  *
	  * @return bool
	  */
	public function has( $name ) {
		return MediaWikiServices::getInstance()->has( $name );
	}

	/**
	 * Returns the Config object containing the bootstrap configuration.
	 * Bootstrap configuration would typically include database credentials
	 * and other information that may be needed before the ConfigFactory
	 * service can be instantiated.
	 *
	 * @note This should only be used during bootstrapping, in particular
	 * when creating the MainConfig service. Application logic should
	 * use getMainConfig() to get a Config instances.
	 *
	 * @since 1.27
	 * @return Config
	 */
	public function getBootstrapConfig() {
		return $this->getService( 'BootstrapConfig' );
	}

	/**
	 * @since 1.27
	 * @return ConfigFactory
	 */
	public function getConfigFactory() {
		return $this->getService( 'ConfigFactory' );
	}

	/**
	 * Returns the Config object that provides configuration for MediaWiki core.
	 * This may or may not be the same object that is returned by getBootstrapConfig().
	 *
	 * @since 1.27
	 * @return Config
	 */
	public function getMainConfig() {
		return $this->getService( 'MainConfig' );
	}

	/**
	 * @since 1.27
	 * @return SiteLookup
	 */
	public function getSiteLookup() {
		return $this->getService( 'SiteLookup' );
	}

	/**
	 * @since 1.27
	 * @return SiteStore
	 */
	public function getSiteStore() {
		return $this->getService( 'SiteStore' );
	}

	/**
	 * @since 1.28
	 * @return InterwikiLookup
	 */
	public function getInterwikiLookup() {
		return $this->getService( 'InterwikiLookup' );
	}

	/**
	 * @since 1.27
	 * @return IBufferingStatsdDataFactory
	 */
	public function getStatsdDataFactory() {
		return $this->getService( 'StatsdDataFactory' );
	}

	/**
	 * @since 1.27
	 * @return EventRelayerGroup
	 */
	public function getEventRelayerGroup() {
		return $this->getService( 'EventRelayerGroup' );
	}

	/**
	 * @since 1.27
	 * @return SearchEngine
	 */
	public function newSearchEngine() {
		// New engine object every time, since they keep state
		return $this->getService( 'SearchEngineFactory' )->create();
	}

	/**
	 * @since 1.27
	 * @return SearchEngineFactory
	 */
	public function getSearchEngineFactory() {
		return $this->getService( 'SearchEngineFactory' );
	}

	/**
	 * @since 1.27
	 * @return SearchEngineConfig
	 */
	public function getSearchEngineConfig() {
		return $this->getService( 'SearchEngineConfig' );
	}

	/**
	 * @since 1.27
	 * @return SkinFactory
	 */
	public function getSkinFactory() {
		return $this->getService( 'SkinFactory' );
	}

	/**
	 * @since 1.28
	 * @return LBFactory
	 */
	public function getDBLoadBalancerFactory() {
		return $this->getService( 'DBLoadBalancerFactory' );
	}

	/**
	 * @since 1.28
	 * @return LoadBalancer The main DB load balancer for the local wiki.
	 */
	public function getDBLoadBalancer() {
		return $this->getService( 'DBLoadBalancer' );
	}

	/**
	 * @since 1.28
	 * @return WatchedItemStoreInterface
	 */
	public function getWatchedItemStore() {
		return $this->getService( 'WatchedItemStore' );
	}

	/**
	 * @since 1.28
	 * @return WatchedItemQueryService
	 */
	public function getWatchedItemQueryService() {
		return $this->getService( 'WatchedItemQueryService' );
	}

	/**
	 * @since 1.28
	 * @return CryptRand
	 */
	public function getCryptRand() {
		return $this->getService( 'CryptRand' );
	}

	/**
	 * @since 1.28
	 * @return CryptHKDF
	 */
	public function getCryptHKDF() {
		return $this->getService( 'CryptHKDF' );
	}

	/**
	 * @since 1.28
	 * @return MediaHandlerFactory
	 */
	public function getMediaHandlerFactory() {
		return $this->getService( 'MediaHandlerFactory' );
	}

	/**
	 * @since 1.28
	 * @return MimeAnalyzer
	 */
	public function getMimeAnalyzer() {
		return $this->getService( 'MimeAnalyzer' );
	}

	/**
	 * @since 1.28
	 * @return ProxyLookup
	 */
	public function getProxyLookup() {
		return $this->getService( 'ProxyLookup' );
	}

	/**
	 * @since 1.29
	 * @return Parser
	 */
	public function getParser() {
		return $this->getService( 'Parser' );
	}

	/**
	 * @since 1.30
	 * @return ParserCache
	 */
	public function getParserCache() {
		return $this->getService( 'ParserCache' );
	}

	/**
	 * @since 1.28
	 * @return GenderCache
	 */
	public function getGenderCache() {
		return $this->getService( 'GenderCache' );
	}

	/**
	 * @since 1.28
	 * @return LinkCache
	 */
	public function getLinkCache() {
		return $this->getService( 'LinkCache' );
	}

	/**
	 * @since 1.28
	 * @return LinkRendererFactory
	 */
	public function getLinkRendererFactory() {
		return $this->getService( 'LinkRendererFactory' );
	}

	/**
	 * LinkRenderer instance that can be used
	 * if no custom options are needed
	 *
	 * @since 1.28
	 * @return LinkRenderer
	 */
	public function getLinkRenderer() {
		return $this->getService( 'LinkRenderer' );
	}

	/**
	 * @since 1.28
	 * @return TitleFormatter
	 */
	public function getTitleFormatter() {
		return $this->getService( 'TitleFormatter' );
	}

	/**
	 * @since 1.28
	 * @return TitleParser
	 */
	public function getTitleParser() {
		return $this->getService( 'TitleParser' );
	}

	/**
	 * @since 1.28
	 * @return BagOStuff
	 */
	public function getMainObjectStash() {
		return $this->getService( 'MainObjectStash' );
	}

	/**
	 * @since 1.28
	 * @return WANObjectCache
	 */
	public function getMainWANObjectCache() {
		return $this->getService( 'MainWANObjectCache' );
	}

	/**
	 * @since 1.28
	 * @return BagOStuff
	 */
	public function getLocalServerObjectCache() {
		return $this->getService( 'LocalServerObjectCache' );
	}

	/**
	 * @since 1.28
	 * @return VirtualRESTServiceClient
	 */
	public function getVirtualRESTServiceClient() {
		return $this->getService( 'VirtualRESTServiceClient' );
	}

	/**
	 * @since 1.29
	 * @return ConfiguredReadOnlyMode
	 */
	public function getConfiguredReadOnlyMode() {
		return $this->getService( 'ConfiguredReadOnlyMode' );
	}

	/**
	 * @since 1.29
	 * @return ReadOnlyMode
	 */
	public function getReadOnlyMode() {
		return $this->getService( 'ReadOnlyMode' );
	}

	/**
	 * @since 1.30
	 * @return CommandFactory
	 */
	public function getShellCommandFactory() {
		return $this->getService( 'ShellCommandFactory' );
	}

	/**
	 * @since 1.31
	 * @return ExternalStoreFactory
	 */
	public function getExternalStoreFactory() {
		return $this->getService( 'ExternalStoreFactory' );
	}

	/**
	 * @since 1.31
	 * @return RevisionStore
	 */
	public function getRevisionStore() {
		return $this->getService( 'RevisionStore' );
	}

	/**
	 * @since 1.32
	 * @return Language
	 */
	public function getContentLanguage() {
		return $this->getService( 'ContentLanguage' );
	}

	/**
	 * @since 1.31
	 * @return BlobStoreFactory
	 */
	public function getBlobStoreFactory() {
		return $this->getService( 'BlobStoreFactory' );
	}

	/**
	 * @since 1.31
	 * @return BlobStore
	 */
	public function getBlobStore() {
		return $this->getService( '_SqlBlobStore' );
	}

	/**
	 * @since 1.31
	 * @return RevisionLookup
	 */
	public function getRevisionLookup() {
		return $this->getService( 'RevisionLookup' );
	}

	/**
	 * @since 1.31
	 * @return RevisionFactory
	 */
	public function getRevisionFactory() {
		return $this->getService( 'RevisionFactory' );
	}

	/**
	 * @since 1.31
	 * @return NameTableStore
	 */
	public function getContentModelStore() {
		return $this->getService( 'ContentModelStore' );
	}

	/**
	 * @since 1.31
	 * @return NameTableStore
	 */
	public function getSlotRoleStore() {
		return $this->getService( 'SlotRoleStore' );
	}

	/**
	 * @since 1.31
	 * @return PreferencesFactory
	 */
	public function getPreferencesFactory() {
		return $this->getService( 'PreferencesFactory' );
	}

	/**
	 * @since 1.31
	 * @return HttpRequestFactory
	 */
	public function getHttpRequestFactory() {
		return $this->getService( 'HttpRequestFactory' );
	}

	/**
	 * @since 1.31
	 * @return CommentStore
	 */
	public function getCommentStore() {
		return $this->getService( 'CommentStore' );
	}

	/**
	 * @since 1.31
	 * @return ActorMigration
	 */
	public function getActorMigration() {
		return $this->getService( 'ActorMigration' );
	}

	/**
	 * @since 1.33
	 * @return PermissionManager
	 */
	public function getPermissionManager() {
		return $this->getService( 'PermissionManager' );
	}

	/**
	 * @since 1.34
	 * @return NamespaceInfo
	 */
	public function getNamespaceInfo(): NamespaceInfo {
		return $this->getService( 'NamespaceInfo' );
	}

	/**
	 * @since 1.35
	 * @return SpecialPageFactory
	 */
	public function getSpecialPageFactory(): SpecialPageFactory {
		return $this->getService( 'SpecialPageFactory' );
	}
}
