<?php

namespace BlueSpice\Permission\Role;
use BlueSpice\Permission\Role\IRole;
use BlueSpice\Permission\Role\Role;

/**
 * This class controls all the operation
 * on the roles. All role interaction should
 * be done using this manager.
 * It should be accessed over MediaWikiServices,
 * where it is registered as "BSRoleManager"
 */
class Manager {
	const ROLE_GRANT = true;
	const ROLE_DENY = false;

	private static $instance;

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
	 * @var BlueSpice\Permission\Registry
	 */
	protected $permissionRegistry;

	protected $roles = [];

	protected function __construct( &$groupPermission, &$roleGroups, &$roleSystemEnabled ) {
		$this->groupRoles =& $roleGroups;
		$this->groupPermissions =& $groupPermission;
		$this->roleSystemEnabled =& $roleSystemEnabled;
		$this->permissionRegistry = \MediaWiki\MediaWikiServices::getInstance()->getService(
			'BSPermissionRegistry'
		);

		$this->makeRoles();
	}

	/**
	 * Gets the instance of the manager
	 *
	 * @param array $groupPermission
	 * @param array $roleGroups
	 * @param bool $roleSystemEnabled
	 * @return BlueSpice\Permission\Role\Manager
	 */
	public static function getInstance( &$groupPermission, &$roleGroups, &$roleSystemEnabled ) {
		if( self::$instance === null ) {
			self::$instance = self::newInstance( $groupPermission, $roleGroups, $roleSystemEnabled );
		}
		return self::$instance;
	}

	protected static function newInstance( &$groupPermission, &$roleGroups, &$roleSystemEnabled ) {
		return new self( $groupPermission, $roleGroups, $roleSystemEnabled );
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
	 * @param srting $group GroupName
	 */
	public function removeRoleFromGroup( $role, $group ) {
		if( isset( $this->groupRoles[ $group ][ $role ] ) ) {
			unset( $this->groupRoles[ $group ][ $role ] );
		}
	}

	/**
	 * Registeres new role
	 *
	 * @param string $roleName
	 * @param array $permissions
	 * @param array $groups
	 * If this parameter is set, all groups passed will
	 * immediately be assigned the role
	 * @param bool $assignToSysop
	 * If false, and no groups are passed,
	 * role will not be assigned to sysop
	 * @param BlueSpice\Permission\Role\IRole|null $roleObject
	 * Enables extensions to register custom role objects
	 */
	public function registerRole( $roleName, $permissions = [], $groups = [], $assignToSysop = false, $roleObject = null ) {
		if( $roleObject == null || ( $roleObject instanceof IRole ) == false ) {
			$roleObject = Role::newFromNameAndPermissions( $roleName, $permissions );
		}
		$this->addRole( $roleObject );
		if( empty( $groups ) === true && $assignToSysop === true ) {
			$this->groupRoles[ 'sysop' ][ $roleName ] = true;
		} else {
			foreach( $groups as $groupName => $active ) {
				$this->assignRoleToGroup( $roleName, $groupName, $active );
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
	 * @param string $permission
	 * @param array $roles Roles to which to add the permission
	 */
	public function addPermissionToRoles( $permission, $roles ) {
		$oAdminRole = $this->getRole( 'admin' );
		$oAdminRole->addPermission( $permission );
		if( isset( $roles ) ) {
			foreach( $roles as $role ) {
				if( $this->roleExists ( $role ) === false ) {
					$this->registerRole( $role, [ $permission ], [], false );
				}
				$roleObject = $this->getRole( $role );
				if( $roleObject instanceof Role\IRole ) {
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

	protected function makeRoles () {
		foreach( $this->permissionRegistry->getPermissions() as
				$permissionName => $permissionDescription ) {
			if( empty( $permissionDescription->getRoles() ) ) {
				continue;
			}
			foreach( $permissionDescription->getRoles() as $roleName ) {

				if( $this->roleExists ( $roleName ) === false ) {
					$this->registerRole( $roleName );
				}
				$roleObject = $this->getRole( $roleName );
				$roleObject->addPermission( $permissionName );
			}
		}
	}
}
