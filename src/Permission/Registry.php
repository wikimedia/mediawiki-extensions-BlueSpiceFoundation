<?php

namespace BlueSpice\Permission;
use BlueSpice\Permission;

class Registry {
	private static $instance;
	protected $permissionConfigDefault;
	protected $permissionConfig = [];
	protected $permissions = [];

	protected function __construct( $permissionConfigDefault, $permissionConfig ) {
		$this->permissionConfigDefault = $permissionConfigDefault;
		$this->permissionConfig = is_array( $permissionConfig ) ? $permissionConfig : [];
		$this->init();
	}

	protected function init () {
		//Add default permissions
		foreach( $this->permissionConfigDefault as $permissionName => $config ) {
			$description = new Description( $permissionName, $config );
			$this->addPermission( $permissionName, $description );
		}
		//Add permissions from other extensions
		//This is run after default config has been set
		//to be used to override default permission definition
		//Include pemissions from $bsgPermissionConfig as well
		$permissionConfigFromExtensions = \ExtensionRegistry::getInstance()->getAttribute( 'BlueSpiceFoundationPermissionRegistry' );
		if( is_array( $permissionConfigFromExtensions ) ) {
			$this->permissionConfig = array_merge(
				$permissionConfigFromExtensions,
				$this->permissionConfig
			);
		}

		foreach( $this->permissionConfig as $permissionName => $config ) {
			//We dont want to override all the configuration params
			//only ones that differ from default config
			if( isset( $this->permissionConfigDefault[ $permissionName ] ) ) {
				$config = array_merge(
					$this->permissionConfigDefault[ $permissionName ],
					$config
				);
			}

			$description = new Description( $permissionName, $config );

			$this->addPermission( $permissionName, $description );
		}
	}

	/**
	 * Gets the instance the Registry
	 *
	 * @param array $defaultPermissionConfig
	 * @param array $permissionConfig
	 * @return type
	 */
	public static function getInstance( $defaultPermissionConfig, $permissionConfig ) {
		if( self::$instance === null ) {
			self::$instance = self::newInstance( $defaultPermissionConfig, $permissionConfig );
		}
		return self::$instance;
	}

	protected static function newInstance( $defaultPermissionConfig, $permissionConfig ) {
		return new static( $defaultPermissionConfig, $permissionConfig );
	}

	/**
	 * Adds permission to registry
	 *
	 * @param string $name
	 * @param \BlueSpice\Permission\IDescription $description
	 */
	public function addPermission( $name, IDescription $description ) {
		$this->permissions[ $name ] = $description;
	}

	/**
	 * Gets Description object for permission name
	 *
	 * @param string $name
	 * @return \BlueSpice\Permission\Description|null
	 */
	public function getPermission( $name ) {
		if( isset( $this->permissions[ $name ] ) ) {
			return $this->permissions[ $name ];
		}
		return null;
	}

	/**
	 * Gets all registered permission objects
	 *
	 * @return array
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 * Returns the names of permissions
	 * @return array
	 */
	public function getPermissionNames() {
		return array_keys( $this->permissions );
	}
}
