<?php

namespace BlueSpice;

use Exception;
use MediaWiki\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;
use Status;

class DynamicSettingsManager {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	private $providerRegistry;

	/**
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/** @var IDynamicSettings[]|null */
	private $instances = null;

	/**
	 *
	 * @return DynamicSettingsManager
	 */
	public static function factory() {
		if ( !isset( $GLOBALS['BSDynamicSettingsPreOverrides'] ) ) {
			$GLOBALS['BSDynamicSettingsPreOverrides'] = [];
		}
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationDynamicSettingsRegistry',
			null,
			// `$bsgExtensionAttributeRegistryOverrides` must not be accessed
			// at `manifest.callback` time. So we use a maybe predefined config
			// that was set before wfLoadExtension( 'BlueSpiceFoundation' ) was
			// added. This variable will later not be available in \Config
			$GLOBALS['BSDynamicSettingsPreOverrides']
		);

		return new DynamicSettingsManager( $registry );
	}

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $providerRegistry
	 * @param LoggerInterface|null $logger
	 */
	public function __construct( $providerRegistry, $logger = null ) {
		$this->providerRegistry = $providerRegistry;
		$this->logger = $logger;

		if ( $this->logger === null ) {
			$this->logger = LoggerFactory::getInstance( 'BlueSpice' );
		}
	}

	/**
	 *
	 * @param array &$globals Usually $GLOBALS
	 * @return void
	 * @deprecated 4.3 - Passing $GLOBALS as reference not supported by PHP 8.1 anymore.
	 * All implementations of `IDynamicSettings` should access `$GLOBALS` directly.
	 */
	public function applyAll( &$globals ) {
		$instances = $this->getAllInstances();
		foreach ( $instances as $instance ) {
			$instance->apply( $globals );
		}
		if ( !empty( $globals ) ) {
			wfDeprecated( __FUNCTION__, '4.3', 'Passed in `$globals` must not be altered anymore!' );
		}
	}

	/**
	 * @return string[]
	 * @throws Exception
	 */
	public function getAllKeys() {
		return array_keys( $this->getAllInstances() );
	}

	/**
	 *
	 * @return IDynamicSettings[]
	 */
	private function getAllInstances() {
		if ( $this->instances === null ) {
			$factoryCallbacks = $this->providerRegistry->getAllValues();
			foreach ( $factoryCallbacks as $factoryKey => $factoryCallback ) {
				// Filter out `@note`
				// TODO: Maybe add such a feature to `ExtensionAttributeBasedRegistry` directly
				if ( strpos( $factoryKey, '@' ) === 0 ) {
					continue;
				}
				if ( !is_callable( $factoryCallback ) ) {
					throw new Exception( "Factory of `$factoryKey` not callable!" );
				}
				/** @var IDynamicSettings */
				$settings = call_user_func_array( $factoryCallback, [ $this->logger ] );
				if ( $settings instanceof IDynamicSettings === false ) {
					throw new Exception(
						"Factory of `$factoryKey` did not return an `IDynamicSettings` object!"
					);
				}

				$this->instances[$factoryKey] = $settings;
			}
		}

		return $this->instances;
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return Status
	 */
	public function persist( $key, $data ) {
		$instances = $this->getAllInstances();
		if ( isset( $instances[$key] ) ) {
			$instances[$key]->setData( $data );
			return $instances[$key]->persist();
		}

		throw new Exception( "Could not find instance for `$key`" );
	}

	/**
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function fetch( $key ) {
		$instances = $this->getAllInstances();
		if ( isset( $instances[$key] ) ) {
			return $instances[$key]->fetch();
		}

		throw new Exception( "Could not find instance for `$key`" );
	}

}
