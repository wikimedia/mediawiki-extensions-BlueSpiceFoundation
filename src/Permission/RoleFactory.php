<?php

namespace BlueSpice\Permission;

use BlueSpice\Permission\Role\Generic;

class RoleFactory {
	/**
	 * @var array
	 */
	protected $roleCallbacks = [];

	/**
	 * @var PermissionRegistry
	 */
	protected $permissionRegistry;

	/**
	 *
	 * @param array $roleCallbacks
	 * @param PermissionRegistry $permissionRegistry
	 */
	public function __construct( $roleCallbacks, $permissionRegistry ) {
		$this->roleCallbacks = $roleCallbacks;
		$this->permissionRegistry = $permissionRegistry;
	}

	/**
	 * Creates instance of the given role
	 *
	 * @param string $name
	 * @return IRole
	 */
	public function makeRole( $name ) {
		if ( !isset( $this->roleCallbacks[$name] ) ) {
			return $this->newGeneric( $name );
		}

		$callback = $this->roleCallbacks[$name];
		if ( !is_callable( $callback ) ) {
			return $this->newGeneric( $name );
		}
		$role = call_user_func_array( $callback, [ $this->permissionRegistry ] );
		if ( !$role instanceof IRole ) {
			return $this->newGeneric( $name );
		}
		return $role;
	}

	/**
	 * @param string $name
	 * @return IRole
	 */
	protected function newGeneric( $name ) {
		return new Generic( $name, $this->permissionRegistry );
	}
}
