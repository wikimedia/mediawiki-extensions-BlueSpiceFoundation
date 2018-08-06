<?php

namespace BlueSpice;

class ExtensionAttributeBasedRegistry implements IRegistry {

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
	 * @param string $attribName
	 * @param \ExtensionRegistry|null $extensionRegistry
	 */
	public function __construct( $attribName, $extensionRegistry = null ) {
		$this->attribName = $attribName;
		$this->extensionRegistry = $extensionRegistry;
		if( $this->extensionRegistry === null ) {
			$this->extensionRegistry = \ExtensionRegistry::getInstance();
		}
	}

	/**
	 *
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function getValue( $key, $default = '' ) {
		$registry = $this->extensionRegistry->getAttribute( $this->attribName );
		$value = isset( $registry[$key] ) ? $registry[$key] : $default;

		if( is_array( $value ) ) {
			//Attributes get merged together instead of being overwritten,
			//so just take the last one
			$value = end( $value );
		}

		return (string)$value;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getAllKeys() {
		$registry = $this->extensionRegistry->getAttribute( $this->attribName );
		return array_keys( $registry );
	}
}
