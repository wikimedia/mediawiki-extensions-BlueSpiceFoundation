<?php

namespace BlueSpice\Hook\SearchGetNearMatchComplete;

class ApplyNearMatchLockdown extends \BlueSpice\Hook\SearchGetNearMatchComplete {
	protected $namespaceRolesLockdown;
	protected $permissionRegistry;
	protected $user;

	/**
	 * Checks if user has permission to view
	 * title that has been found
	 *
	 * @return boolean
	 */
	protected function doProcess() {
		$this->setUp();

		if( empty( $this->namespaceRolesLockdown ) ) {
			return true;
		}
		if( $this->title == null ) {
			return true;
		}

		$titleNs = $this->title->getNamespace();
		$permissionObject = $this->permissionRegistry->getPermission( 'read' );
		$permissionRoles = $permissionObject->getRoles();

		$userGroups = $this->user->getEffectiveGroups();

		if( isset( $this->namespaceRolesLockdown[ $titleNs ] ) === false ) {
			return true;
		}

		$isAllowed = false;
		foreach( $this->namespaceRolesLockdown[ $titleNs ] as $roleName => $groups ) {
			if( in_array( $roleName, $permissionRoles ) == false ) {
				continue;
			}
			if( array_intersect ( $groups, $userGroups ) ) {
				return true;
			}
		}

		$this->title = null;
		return false;
	}

	protected function setUp () {
		$config = $this->getConfig();
		$this->namespaceRolesLockdown = $config->get( 'NamespaceRolesLockdown' );

		$this->permissionRegistry = $this->getServices()->getService(
			'BSPermissionRegistry'
		);

		$this->user = $this->getContext()->getUser();
	}
}
