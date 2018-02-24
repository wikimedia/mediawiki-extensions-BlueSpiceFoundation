<?php
namespace BlueSpice\Permission\Role;
use BlueSpice\Permission;

/**
 * Generic class for roles
 */
class Role implements IRole {
	protected $name;
	protected $permissions;

	protected function __construct( $name, $permissions = [] ) {
		$this->name = $name;
		$this->permissions = $permissions;
	}

	/**
	 * Returns the name of the Role
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
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
	 * Creates a new instance of a Role
	 * using provided name and permission set
	 *
	 * @param string $name
	 * @param array $permissions
	 * @return \BlueSpice\Permission\Role
	 */
	public static function newFromNameAndPermissions( $name, $permissions = [] ) {
		return new self( $name, $permissions );
	}

	/**
	 * Creates new instance of a Role
	 * using permissions from global default config.
	 *
	 * @param string $name
	 * @param array $defaultRoleConfig Array of default permission for roles
	 * @return \BlueSpice\Permission\Role|null if no default permissions
	 * are set for this role name
	 */
	public static function newFromDefaultConfig( $name, $defaultRoleConfig ) {
		if( isset( $defaultRoleConfig[ $name ] ) ) {
			return new self( $name, $defaultRoleConfig[ $name ] );
		}
		return null;
	}

	/**
	 * Adds single permission to the role
	 * @param type $sPermission
	 */
	public function addPermission($sPermission) {
		$this->permissions[] = $sPermission;
	}

	/**
	 * Removes single permission from the role
	 * @param type $sPermission
	 */
	public function removePermission ( $sPermission ) {
		if( isset( $this->permissions[ $sPermission] ) ) {
			unset( $this->permissions[ $sPermission] );
		}
	}
}
