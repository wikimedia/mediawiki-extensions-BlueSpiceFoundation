<?php

namespace BlueSpice\Hook\GetUserPermissionsErrors;

class ApplyNamespaceRolesLockdown extends \BlueSpice\Hook\GetUserPermissionsErrors {
	protected $namespaceRolesLockdown;
	protected $whitelistRead;
	protected $languageObject;
	protected $permissionRegistry;

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

		$permissionObject = $this->permissionRegistry->getPermission( $this->action );
		if( $permissionObject === null ) {
			//in case permission is not registered with BlueSpice\Permission\Registry
			return true;
		}
		$actionRoles = $permissionObject->getRoles();

		if( empty( $actionRoles ) ) {
			return true;
		}

		$userGroups = $this->user->getEffectiveGroups();

		$titleNS = $this->title->getNamespace();
		$affectedNamespaces = array_keys( $this->namespaceRolesLockdown );

		//If there are no per-ns roles assigned for this ns dont block
		if( in_array( $titleNS, $affectedNamespaces ) == false ) {
			return true;
		}

		$allowedGroups = [];
		foreach( $this->namespaceRolesLockdown as $ns => $roles ) {
			if( $ns != $titleNS ) {
				continue;
			}
			foreach( $roles as $roleName => $groups ) {
				$allowedGroups = array_merge( $allowedGroups, $groups );
				if( array_intersect( $groups, $userGroups ) ) {
					return true;
				}
			}
		}

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

		$mainConfig = $this->getServices()->getMainConfig();
		$this->whitelistRead = $config->get( 'WhitelistRead' );

		$this->permissionRegistry = $this->getServices()->getService(
			'BSPermissionRegistry'
		);

		$this->languageObject = $this->getContext()->getLanguage();
	}
}