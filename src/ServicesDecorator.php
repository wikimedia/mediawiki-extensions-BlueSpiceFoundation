<?php

namespace BlueSpice;

use MediaWiki\MediaWikiServices;
use MediaWiki\Services\ServiceContainer;

class ServicesDecorator extends ServiceContainer {

	/**
	 * @var Services|null
	 */
	protected static $instance = null;

	/**
	 * Returns the global default instance of the top level service locator.
	 *
	 * @since 1.27
	 *
	 * The default instance is initialized using the service instantiator functions
	 * defined in ServiceWiring.php.
	 *
	 * @note This should only be called by static functions! The instance returned here
	 * should not be passed around! Objects that need access to a service should have
	 * that service injected into the constructor, never a service locator!
	 *
	 * @return Services
	 */
	public static function getInstance() {
		if ( static::$instance === null ) {
			static::$instance = static::newInstance( MediaWikiServices::getInstance() );
		}

		return static::$instance;
	}

	/**
	 * Resets instance when new original instance is resetted
	 *
	 * @param type \MediaWikiServices $services
	 */
	public static function resetInstance( $services ) {
		static::$instance = static::newInstance( $services );
	}

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $decoratedServices = null;

	public function __construct( $services ) {
		$this->decoratedServices = $services;
	}

	/**
	 * @params MediaWikiServices
	 * @return Services
	 * @throws MWException
	 * @throws \FatalError
	 */
	private static function newInstance( $services ) {
		$instance = new static( $services );

		\Hooks::run( 'BlueSpiceServices', [ $instance ] );

		return $instance;
	}

	/**
	 * Returns a service object of the kind associated with $name.
	 * Services instances are instantiated lazily, on demand.
	 * This method may or may not return the same service instance
	 * when called multiple times with the same $name.
	 *
	 * @note Rather than calling this method directly, it is recommended to provide
	 * getters with more meaningful names and more specific return types, using
	 * a subclass or wrapper.
	 *
	 * @see redefineService().
	 *
	 * @param string $name The service name
	 *
	 * @throws NoSuchServiceException if $name is not a known service.
	 * @throws ContainerDisabledException if this container has already been destroyed.
	 * @throws ServiceDisabledException if the requested service has been disabled.
	 *
	 * @return object The service instance
	 */
	public function getService( $name ) {
		return $this->decoratedServices->getService( $name );
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
	 * @return \Config
	 */
	public function getBootstrapConfig() {
		return $this->decoratedServices->getService( 'BootstrapConfig' );
	}

	/**
	 * @since 1.27
	 * @return \ConfigFactory
	 */
	public function getConfigFactory() {
		return $this->decoratedServices->getService( 'ConfigFactory' );
	}

	/**
	 * Returns the Config object that provides configuration for MediaWiki core.
	 * This may or may not be the same object that is returned by getBootstrapConfig().
	 *
	 * @since 1.27
	 * @return \Config
	 */
	public function getMainConfig() {
		return $this->decoratedServices->getService( 'MainConfig' );
	}

	/**
	 * @since 1.27
	 * @return \SiteLookup
	 */
	public function getSiteLookup() {
		return $this->decoratedServices->getService( 'SiteLookup' );
	}

	/**
	 * @since 1.27
	 * @return \SiteStore
	 */
	public function getSiteStore() {
		return $this->decoratedServices->getService( 'SiteStore' );
	}

	/**
	 * @since 1.28
	 * @return \MediaWiki\Interwiki\InterwikiLookup
	 */
	public function getInterwikiLookup() {
		return $this->decoratedServices->getService( 'InterwikiLookup' );
	}

	/**
	 * @since 1.27
	 * @return \IBufferingStatsdDataFactory
	 */
	public function getStatsdDataFactory() {
		return $this->decoratedServices->getService( 'StatsdDataFactory' );
	}

	/**
	 * @since 1.27
	 * @return \EventRelayerGroup
	 */
	public function getEventRelayerGroup() {
		return $this->decoratedServices->getService( 'EventRelayerGroup' );
	}

	/**
	 * @since 1.27
	 * @return \SearchEngine
	 */
	public function newSearchEngine() {
		// New engine object every time, since they keep state
		return $this->decoratedServices->getService( 'SearchEngineFactory' )->create();
	}

	/**
	 * @since 1.27
	 * @return \SearchEngineFactory
	 */
	public function getSearchEngineFactory() {
		return $this->decoratedServices->getService( 'SearchEngineFactory' );
	}

	/**
	 * @since 1.27
	 * @return \SearchEngineConfig
	 */
	public function getSearchEngineConfig() {
		return $this->decoratedServices->getService( 'SearchEngineConfig' );
	}

	/**
	 * @since 1.27
	 * @return \SkinFactory
	 */
	public function getSkinFactory() {
		return $this->decoratedServices->getService( 'SkinFactory' );
	}

	/**
	 * @since 1.28
	 * @return \Wikimedia\Rdbms\LBFactory
	 */
	public function getDBLoadBalancerFactory() {
		return $this->decoratedServices->getService( 'DBLoadBalancerFactory' );
	}

	/**
	 * @since 1.28
	 * @return \LoadBalancer The main DB load balancer for the local wiki.
	 */
	public function getDBLoadBalancer() {
		return $this->decoratedServices->getService( 'DBLoadBalancer' );
	}

	/**
	 * @since 1.28
	 * @return \WatchedItemStoreInterface
	 */
	public function getWatchedItemStore() {
		return $this->decoratedServices->getService( 'WatchedItemStore' );
	}

	/**
	 * @since 1.28
	 * @return \WatchedItemQueryService
	 */
	public function getWatchedItemQueryService() {
		return $this->decoratedServices->getService( 'WatchedItemQueryService' );
	}

	/**
	 * @since 1.28
	 * @return \CryptRand
	 */
	public function getCryptRand() {
		return $this->decoratedServices->getService( 'CryptRand' );
	}

	/**
	 * @since 1.28
	 * @return \CryptHKDF
	 */
	public function getCryptHKDF() {
		return $this->decoratedServices->getService( 'CryptHKDF' );
	}

	/**
	 * @since 1.28
	 * @return \MediaHandlerFactory
	 */
	public function getMediaHandlerFactory() {
		return $this->decoratedServices->getService( 'MediaHandlerFactory' );
	}

	/**
	 * @since 1.28
	 * @return \MimeAnalyzer
	 */
	public function getMimeAnalyzer() {
		return $this->decoratedServices->getService( 'MimeAnalyzer' );
	}

	/**
	 * @since 1.28
	 * @return \ProxyLookup
	 */
	public function getProxyLookup() {
		return $this->decoratedServices->getService( 'ProxyLookup' );
	}

	/**
	 * @since 1.29
	 * @return \Parser
	 */
	public function getParser() {
		return $this->decoratedServices->getService( 'Parser' );
	}

	/**
	 * @since 1.30
	 * @return \ParserCache
	 */
	public function getParserCache() {
		return $this->decoratedServices->getService( 'ParserCache' );
	}

	/**
	 * @since 1.28
	 * @return \GenderCache
	 */
	public function getGenderCache() {
		return $this->decoratedServices->getService( 'GenderCache' );
	}

	/**
	 * @since 1.28
	 * @return \LinkCache
	 */
	public function getLinkCache() {
		return $this->decoratedServices->getService( 'LinkCache' );
	}

	/**
	 * @since 1.28
	 * @return \MediaWiki\Linker\LinkRendererFactory
	 */
	public function getLinkRendererFactory() {
		return $this->decoratedServices->getService( 'LinkRendererFactory' );
	}

	/**
	 * LinkRenderer instance that can be used
	 * if no custom options are needed
	 *
	 * @since 1.28
	 * @return \MediaWiki\Linker\LinkRenderer
	 */
	public function getLinkRenderer() {
		return $this->decoratedServices->getService( 'LinkRenderer' );
	}

	/**
	 * @since 1.28
	 * @return \TitleFormatter
	 */
	public function getTitleFormatter() {
		return $this->decoratedServices->getService( 'TitleFormatter' );
	}

	/**
	 * @since 1.28
	 * @return \TitleParser
	 */
	public function getTitleParser() {
		return $this->decoratedServices->getService( 'TitleParser' );
	}

	/**
	 * @since 1.28
	 * @return \BagOStuff
	 */
	public function getMainObjectStash() {
		return $this->decoratedServices->getService( 'MainObjectStash' );
	}

	/**
	 * @since 1.28
	 * @return \WANObjectCache
	 */
	public function getMainWANObjectCache() {
		return $this->decoratedServices->getService( 'MainWANObjectCache' );
	}

	/**
	 * @since 1.28
	 * @return \BagOStuff
	 */
	public function getLocalServerObjectCache() {
		return $this->decoratedServices->getService( 'LocalServerObjectCache' );
	}

	/**
	 * @since 1.28
	 * @return \VirtualRESTServiceClient
	 */
	public function getVirtualRESTServiceClient() {
		return $this->decoratedServices->getService( 'VirtualRESTServiceClient' );
	}

	/**
	 * @since 1.29
	 * @return \ConfiguredReadOnlyMode
	 */
	public function getConfiguredReadOnlyMode() {
		return $this->decoratedServices->getService( 'ConfiguredReadOnlyMode' );
	}

	/**
	 * @since 1.29
	 * @return \ReadOnlyMode
	 */
	public function getReadOnlyMode() {
		return $this->decoratedServices->getService( 'ReadOnlyMode' );
	}

	/**
	 * @since 1.30
	 * @return \CommandFactory
	 */
	public function getShellCommandFactory() {
		return $this->decoratedServices->getService( 'ShellCommandFactory' );
	}

	/**
	 * @since 1.31
	 * @return \ExternalStoreFactory
	 */
	public function getExternalStoreFactory() {
		return $this->decoratedServices->getService( 'ExternalStoreFactory' );
	}

	/**
	 * @since 1.31
	 * @return \MediaWiki\Storage\RevisionStore
	 */
	public function getRevisionStore() {
		return $this->decoratedServices->getService( 'RevisionStore' );
	}

}
