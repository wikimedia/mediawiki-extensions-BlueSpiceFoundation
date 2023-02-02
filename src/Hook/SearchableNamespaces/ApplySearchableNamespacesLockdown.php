<?php

namespace BlueSpice\Hook\SearchableNamespaces;

class ApplySearchableNamespacesLockdown extends \BlueSpice\Hook\SearchableNamespaces {
	protected $namespaceRolesLockdown;
	protected $permissionRegistry;
	protected $user;

	/**
	 * Checks if current user has permissions
	 * to search in particular namespace
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->setUp();

		if ( empty( $this->namespaceRolesLockdown ) ) {
			return true;
		}

		$permissionObject = $this->permissionRegistry->getPermission( 'read' );
		$permissionRoles = $permissionObject->getRoles();

		$userGroups = $this->getServices()
			->getUserGroupManager()
			->getUserEffectiveGroups( $this->user );

		foreach ( $this->namespaces as $nsId => $nsName ) {
			if ( !isset( $this->namespaceRolesLockdown[ $nsId ] ) ) {
				continue;
			}
			$lockedRoles = array_intersect(
				$permissionRoles,
				array_keys( $this->namespaceRolesLockdown[ $nsId ] )
			);
			if ( empty( $lockedRoles ) ) {
				// Lockdown does not apply to any role that has this permission
				continue;
			}
			$isAllowed = false;
			foreach ( $this->namespaceRolesLockdown[ $nsId ] as $roleName => $groups ) {
				if ( in_array( $roleName, $permissionRoles ) == false ) {
					continue;
				}
				if ( array_intersect( $groups, $userGroups ) ) {
					$isAllowed = true;
					break;
				}
			}
			if ( !$isAllowed ) {
				unset( $this->namespaces[ $nsId ] );
			}
		}
		return true;
	}

	protected function setUp(): void {
		$config = $this->getConfig();
		$this->namespaceRolesLockdown = $config->get( 'NamespaceRolesLockdown' );

		$this->permissionRegistry = $this->getServices()->getService(
			'BSPermissionRegistry'
		);

		$this->user = $this->getContext()->getUser();
	}
}
