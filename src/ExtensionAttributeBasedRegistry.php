<?php

namespace BlueSpice;

use ExtensionRegistry;

class ExtensionAttributeBasedRegistry implements IRegistry {

	const OVERRIDE_SET = 'set';
	const OVERRIDE_MERGE = 'merge';
	const OVERRIDE_REMOVE = 'remove';

	/**
	 *
	 * @var string
	 */
	protected $attribName = '';

	/**
	 *
	 * @var \ExtensionRegistry
	 */
	protected $extensionRegistry = null;

	/**
	 *
	 * @var array
	 */
	protected $overrides = [];

	/**
	 *
	 * @param string $attribName
	 * @param \ExtensionRegistry|null $extensionRegistry
	 * @param array|null $overrides
	 */
	public function __construct( $attribName, $extensionRegistry = null, $overrides = null ) {
		$this->attribName = $attribName;
		$this->extensionRegistry = $extensionRegistry;
		$this->overrides = $overrides;

		if ( $this->extensionRegistry === null ) {
			$this->extensionRegistry = ExtensionRegistry::getInstance();
		}

		if ( $this->overrides === null ) {
			$services = Services::getInstance();
			$config = $services->getConfigFactory()->makeConfig( 'bsg' );
			$configOverrides = $config->get( 'ExtensionAttributeRegistryOverrides' );

			$this->overrides = [];
			if ( isset( $configOverrides[ $attribName ] ) ) {
				$this->overrides = $configOverrides[ $attribName ];
			}
		}
	}

	/**
	 *
	 * @param string $key
	 * @param string $default
	 * @return string|callable
	 */
	public function getValue( $key, $default = '' ) {
		$registry = $this->getRegistryArray();
		$value = isset( $registry[$key] ) ? $registry[$key] : $default;

		if ( is_array( $value ) ) {
			// Attributes get merged together instead of being overwritten,
			// so just take the last one
			$value = end( $value );
		}

		return $value;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getAllKeys() {
		$registry = $this->getRegistryArray();
		return array_keys( $registry );
	}

	/**
	 *
	 * @return array
	 */
	public function getAllValues() {
		$all = [];
		foreach ( $this->getAllKeys() as $key ) {
			$all[$key] = $this->getValue( $key );
		}
		return $all;
	}

	/**
	 *
	 * @return array
	 */
	protected function getRegistryArray() {
		$registry = $this->extensionRegistry->getAttribute( $this->attribName );
		if ( isset( $this->overrides[static::OVERRIDE_SET ] ) ) {
			$registry = $this->overrides[static::OVERRIDE_SET ];
		} else {
			if ( isset( $this->overrides[static::OVERRIDE_MERGE ] ) ) {
				$registry = array_merge(
					$registry,
					$this->overrides[static::OVERRIDE_MERGE ]
				);
			}
			if ( isset( $this->overrides[static::OVERRIDE_REMOVE ] ) ) {
				foreach ( $this->overrides[static::OVERRIDE_REMOVE ] as $removeKey ) {
					if ( isset( $registry[ $removeKey ] ) ) {
						unset( $registry[ $removeKey ] );
					}
				}
			}
		}

		return $registry;
	}

}
