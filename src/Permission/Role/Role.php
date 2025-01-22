<?php
namespace BlueSpice\Permission\Role;

use BlueSpice\Permission\IRole;
use BlueSpice\Permission\PermissionRegistry;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;

/**
 * Base class for Roles
 */
abstract class Role implements IRole {
	/**
	 *
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var string[]
	 */
	protected $permissions = [];

	/**
	 * @var PermissionRegistry
	 */
	protected $permissionRegistry;

	/**
	 * @param PermissionRegistry $permissionRegistry
	 * @return IRole
	 */
	public static function factory( PermissionRegistry $permissionRegistry ) {
		return new static( $permissionRegistry );
	}

	/**
	 *
	 * @param PermissionRegistry $permissionRegistry
	 */
	protected function __construct( PermissionRegistry $permissionRegistry ) {
		$this->permissionRegistry = $permissionRegistry;
		$this->loadPermissionsFromRegistry();
	}

	/**
	 * Returns the permissions of the Role
	 *
	 * @return array
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 * Adds single permission to the role
	 * @deprecated Since 3.1 - Implement and register role class (implements IRole)
	 * or add permission to role using "BlueSpiceFoundationPermissionRegistry"
	 * attribute or global var
	 * @param string $permission
	 */
	public function addPermission( $permission ) {
		if ( !in_array( $permission, $this->permissions ) ) {
			$this->permissions[] = $permission;
		}
	}

	/**
	 * Creates a new instance of a Role
	 * using provided name and permission set
	 *
	 * @deprecated Since 3.1 - Get role objects over BSRoleFactory service and add permissions
	 * to role using "BlueSpiceFoundationPermissionRegistry" attribute
	 *
	 * @param string $name
	 * @param array $permissions
	 * @return IRole
	 */
	public static function newFromNameAndPermissions( $name, $permissions = [] ) {
		$roleFactory = MediaWikiServices::getInstance()->getService( 'BSRoleFactory' );
		$role = $roleFactory->makeRole( $name );
		foreach ( $permissions as $permission ) {
			$role->addPermission( $permission );
		}
		return $role;
	}

	/**
	 * Creates new instance of a Role
	 * using permissions from global default config.
	 *
	 * @deprecated Since 3.1 - Get role objects over BSRoleFactory service
	 * @param string $name
	 * @param array $defaultRoleConfig Array of default permission for roles
	 * @return Role|null if no default permissions
	 * are set for this role name
	 */
	public static function newFromDefaultConfig( $name, $defaultRoleConfig ) {
		if ( isset( $defaultRoleConfig[ $name ] ) ) {
			return static::newFromNameAndPermissions( $name, $defaultRoleConfig[$name] );
		}
		return null;
	}

	/**
	 * Removes single permission from the role
	 * @deprecated Since 3.1 - Implement and register role class (implements IRole)
	 * or remove roles from permission using "BlueSpiceFoundationPermissionRegistry"
	 * attribute or global var
	 * @param string $permission
	 */
	public function removePermission( $permission ) {
		$index = array_search( $permission, $this->permissions );
		if ( $index !== false ) {
			unset( $this->permissions[ $index ] );
		}
	}

	/**
	 * Loads all permissions that have this role assigned
	 */
	protected function loadPermissionsFromRegistry() {
		$this->loadPermissionsForRole( $this->getName() );
	}

	/**
	 * This is separate method to make overriding roles easier
	 *
	 * @param string $roleName
	 */
	protected function loadPermissionsForRole( $roleName ) {
		foreach (
			$this->permissionRegistry->getPermissions() as $permissionName => $permissionDescription
		) {
			$rolesAssigned = $permissionDescription->getRoles();
			if ( !in_array( $roleName, $rolesAssigned ) ) {
				continue;
			}
			if ( !in_array( $permissionName, $this->permissions ) ) {
				$this->permissions[] = $permissionName;
			}
		}
	}

	/**
	 * Returns the privilege level of the Role
	 *
	 * @return int
	 */
	public function getPrivilegeLevel() {
		return 50;
	}

	/**
	 * @return Message
	 */
	public function getLabel(): Message {
		return Message::newFromKey( 'bs-permission-role-' . $this->getName() );
	}
}
