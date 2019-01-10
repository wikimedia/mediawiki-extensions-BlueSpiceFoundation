<?php

namespace BlueSpice\Hook\GetUserPermissionsErrors;

class ApplyNamespaceRolesLockdown extends \BlueSpice\Hook\GetUserPermissionsErrors {
	/**
	 *
	 * @var array
	 */
	protected $namespaceRolesLockdown;

	/**
	 *
	 * @var array
	 */
	protected $whitelistRead;

	/**
	 *
	 * @var \Language
	 */
	protected $languageObject;

	/**
	 *
	 * @var \BlueSpice\Permission\Role\Manager
	 */
	protected $roleManager;

	/**
	 * Checks if requested action belongs to a role
	 * that is explicitly granted only to some groups
	 *
	 * @return boolean
	 */
	protected function doProcess () {
		$this->setUp();

		if( empty( $this->namespaceRolesLockdown ) ) {
			return true;
		}
		if( $this->title->isUserConfigPage() ) {
			return true;
		}

		if( $this->action == 'read' && is_array( $this->whitelistRead ) ) {
			if( in_array( $this->title->getPrefixedText(), $this->whitelistRead ) ) {
				return true;
			}
		}

		$actionRoles = [];
		$roles = $this->roleManager->getRoleNamesAndPermissions();
		foreach( $roles as $role ) {
			if ( in_array( $this->action, $role['permissions'] ) ) {
				$actionRoles[] = $role['role'];
			}
		}

		if( empty( $actionRoles ) ) {
			return true;
		}

		$userGroups = $this->user->getEffectiveGroups();

		$titleNS = $this->title->getNamespace();
		$affectedNamespaces = array_keys( $this->namespaceRolesLockdown );

		//If there are no per-ns roles assigned for this ns don't block
		if( in_array( $titleNS, $affectedNamespaces ) == false ) {
			return true;
		}

		// Does any of the roles containing this permission have a lockdown.
		$applies = false;
		$allowedGroups = [];
		foreach( $this->namespaceRolesLockdown as $ns => $roles ) {
			if( $ns != $titleNS ) {
				continue;
			}
			foreach( $roles as $roleName => $groups ) {
				$allowedGroups = array_merge( $allowedGroups, $groups );
				if( !in_array( $roleName, $actionRoles ) ) {
					continue;
				}

				// If any of the roles that are under lockdown are containing
				// permission we are testing for, lockdown applies
				$applies = true;
				if( array_intersect( $groups, $userGroups ) ) {
					return true;
				}
			}
		}

		if ( $applies === false ) {
			// Bail out if this permission is not affected by lockdown
			return true;
		}

		$allowedGroups = array_unique( $allowedGroups );
		$this->result = [
			'badaccess-groups',
			$this->languageObject->commaList( $allowedGroups ),
			count( $allowedGroups )
		];

		return false;
	}

	protected function setUp() {
		$config = $this->getConfig();
		$this->namespaceRolesLockdown = $config->get( 'NamespaceRolesLockdown' );

		$this->whitelistRead = $config->get( 'WhitelistRead' );

		$this->roleManager = $this->getServices()->getService(
			'BSRoleManager'
		);

		$this->languageObject = $this->getContext()->getLanguage();
	}
}
