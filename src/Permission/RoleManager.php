<?php

namespace BlueSpice\Permission;

/**
 * This class controls all the operation
 * on the roles. All role interaction should
 * be done using this manager.
 * It should be accessed over MediaWikiServices,
 * where it is registered as "BSRoleManager"
 */
class RoleManager {
	const ROLE_GRANT = true;
	const ROLE_DENY = false;

	/**
	 * Which roles are assigned to which groups
	 *
	 * @var IRole[]
	 */
	protected $groupRoles;

	/**
	 * Mediawiki global $wgGroupPermissions
	 *
	 * @var array
	 */
	protected $groupPermissions;

	/**
	 * Is role system enabled
	 *
	 * @var bool
	 */
	protected $roleSystemEnabled;

	/**
	 * @var PermissionRegistry
	 */
	protected $permissionRegistry;

	/**
	 * @var RoleFactory
	 */
	protected $roleFactory;

	/**
	 * @var array
	 */
	protected $predefinedRoles;

	protected $roles = [];

	/**
	 * Manager constructor.
	 * @param array $groupPermission
	 * @param array $roleGroups
	 * @param boolean $roleSystemEnabled
	 * @param array $predefinedRoles
	 * @param PermissionRegistry $permissionRegistry
	 * @param RoleFactory $roleFactory
	 */
	public function __construct( &$groupPermission, &$roleGroups, &$roleSystemEnabled, $predefinedRoles, $permissionRegistry, $roleFactory ) {
		$this->groupRoles =& $roleGroups;
		$this->groupPermissions =& $groupPermission;
		$this->roleSystemEnabled =& $roleSystemEnabled;
		$this->predefinedRoles = $predefinedRoles;

		$this->permissionRegistry = $permissionRegistry;
		$this->roleFactory = $roleFactory;

		$this->makePredefinedRoles();
		$this->makeRolesFromPermissions();
	}

	/**
	 * Applies role permission.
	 * Removes all the current permissions and
	 * adds permissions from role definitions
	 */
	public function applyRoles() {
		$this->resetGroupPermissions();
		foreach( $this->groupRoles as $group => $roles ) {
			foreach( $roles as $role => $active ) {
				if( $this->roleExists( $role ) === false ) {
					wfDebugLog(
						'BsRoleSystem',
						__CLASS__ . ": Applying role $role failed because it does not exist!"
					);
					continue;
				}
				if( $active ) {
					$this->applyToGroup( $group, $role, self::ROLE_GRANT );
				} else {
					//DS: Maybe used in the future
					#$this->applyToGroup( $group, $role, self::ROLE_DENY );
				}
			}
		}
	}

	/**
	 * Applies role to a single group
	 *
	 * @param string $group Group name
	 * @param string $role Role name
	 * @param bool $grant Grant or deny permissions in the role
	 */
	protected function applyToGroup ( $group, $role, $grant ) {
		$roleObject = $this->getRole( $role );
		$rolePermissions = $roleObject->getPermissions();
		foreach( $rolePermissions as $permission ) {
			$this->groupPermissions[ $group ][ $permission ] = $grant;
		}
	}

	protected function resetGroupPermissions() {
		//All permissions, including 3rd party ones,
		//which are not included in the registry are removed
		$this->groupPermissions = array_map(
			function() {
				return [];
			},
			$this->groupPermissions
		);
	}

	/**
	 * Gets all roles assigned to a group
	 *
	 * @param string $group
	 * @return array
	 */
	public function getGroupRoles( $group = '' ) {
		if( $group && isset( $this->groupRoles[ $group ] ) ) {
			return [ $group => $this->groupRoles[ $group ] ];
		}
		return $this->groupRoles;
	}

	/**
	 * Assigns role to a group
	 *
	 * @param string $role Role name
	 * @param string $group GroupName
	 * @param bool $grant Grant or deny permissions in the role
	 */
	public function assignRoleToGroup( $role, $group, $grant = self::ROLE_GRANT ) {
		$this->groupRoles[ $group ][ $role ] = $grant;
	}

	/**
	 * Removes a role from a group
	 *
	 * @param string $role Role name
	 * @param string $group GroupName
	 */
	public function removeRoleFromGroup( $role, $group ) {
		if( isset( $this->groupRoles[ $group ][ $role ] ) ) {
			unset( $this->groupRoles[ $group ][ $role ] );
		}
	}

	/**
	 * Register new role
	 *
	 * @param IRole
	 * @param array $groups Groups to assign the role to
	 */
	public function registerRole( IRole $role, $groups = [] ) {
		$this->addRole( $role );
		if( !empty( $groups ) ) {
			foreach( $groups as $groupName => $active ) {
				$this->assignRoleToGroup( $role->getName(), $groupName, $active );
			}
		}
	}

	/**
	 * Removes the role
	 *
	 * @param string $roleName
	 */
	public function unregisterRole( $roleName ) {
		if( $this->roleExists( $roleName ) ) {
			$this->removeRole( $roleName );
		}
		foreach( $this->groupRoles as $group => $roles ) {
			foreach( $roles as $role => $granted ) {
				if( $roleName === $role ) {
					$this->removeRoleFromGroup( $role, $group );
				}
			}
		}
	}

	/**
	 * Adds single permission to multiple roles
	 * If a role does not exist, it will be created and
	 * be given the permission
	 *
	 * @deprecated Since 3.1 - Implement and register role class (implements IRole)
	 * or add permission to role using "BlueSpiceFoundationPermissionRegistry" attribute or global var
	 * @param string $permission
	 * @param array $roles Roles to which to add the permission
	 */
	public function addPermissionToRoles( $permission, $roles ) {
		$oAdminRole = $this->getRole( 'admin' );
		$oAdminRole->addPermission( $permission );
		if( isset( $roles ) ) {
			foreach( $roles as $role ) {
				if( $this->roleExists ( $role ) === false ) {
					$roleObject = $this->roleFactory->makeRole( $role );
					$this->registerRole( $roleObject );
				} else {
					$roleObject = $this->getRole( $role );
				}

				if( $roleObject instanceof IRole ) {
					$roleObject->addPermission( $permission );
				}
			}
		}
	}

	/**
	 * Returns whether role system is enabled
	 *
	 * @return bool
	 */
	public function isRoleSystemEnabled() {
		return $this->roleSystemEnabled;
	}

	/**
	 * Enables the role system
	 *
	 * @return void
	 */
	public function enableRoleSystem() {
		$this->roleSystemEnabled = true;
	}

	/**
	 * Disables the role system
	 *
	 * @return void
	 */
	public function disableRoleSystem() {
		$this->roleSystemEnabled = false;
	}

	/**
	 * Returns Role object based on name
	 *
	 * @param string $sName
	 * @return \BlueSpice\Permission\Role\IRole
	 */
	public function getRole ( $role ) {
		if( $this->roleExists ( $role ) ) {
			return $this->roles[ $role ];
		}
		return null;
	}

	protected function addRole ( $roleObject ) {
		$this->roles[ $roleObject->getName() ] = $roleObject;
	}

	protected function roleExists ( $roleName ) {
		if( isset( $this->roles[ $roleName ] ) ) {
			return true;
		}
		return false;
	}

	protected function removeRole ( $roleName ) {
		if( $this->roleExists ( $roleName ) ) {
			unset( $this->roles[ $roleName ] );
		}
	}

	/**
	 * Returns names of all roles as
	 * string array
	 *
	 * @return array
	 */
	public function getRoleNames() {
		return array_keys( $this->roles );
	}

	public function getRoleNamesAndPermissions() {
		$rolesAndPermissions = [];
		foreach( $this->roles as $roleName => $roleObject ) {
			$rolesAndPermissions[] = [
				'role' => $roleName,
				'permissions' => $roleObject->getPermissions()
			];
		}
		return $rolesAndPermissions;
	}

	protected function makePredefinedRoles() {
		foreach( $this->predefinedRoles as $roleName => $class ) {
			$roleObject = $this->roleFactory->makeRole( $roleName );
			$this->registerRole( $roleObject );
		}
	}

	protected function makeRolesFromPermissions() {
		$checked = [];
		foreach( $this->permissionRegistry->getPermissions() as
				 $permissionName => $permissionDescription ) {
			$rolesAssigned = $permissionDescription->getRoles();
			foreach( $rolesAssigned as $role ) {
				if ( in_array( $role, $checked ) ) {
					continue;
				};

				$checked[] = $role;
				if ( $this->isRolePredefined( $role ) === false ) {
					$roleObject = $this->roleFactory->makeRole( $role );
					$this->registerRole( $roleObject );
				}
			}
		}
	}

	protected function isRolePredefined( $roleName ) {
		if ( isset( $this->predefinedRoles[$roleName] ) ) {
			return true;
		}
		return false;
	}
}
