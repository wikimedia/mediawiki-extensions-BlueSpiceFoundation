<?php

namespace BlueSpice\Permission\Lockdown\Module;

use BlueSpice\Permission\Lockdown\IModule;
use BlueSpice\Permission\Lockdown\Module;
use BlueSpice\Permission\RoleManager;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class Namespaces extends Module {

	/**
	 *
	 * @var array
	 */
	protected $allowedGroups = [];

	/**
	 *
	 * @var Manager
	 */
	protected $manager = null;

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param MediaWikiServices $services
	 * @param RoleManager $manager
	 */
	protected function __construct( Config $config, IContextSource $context,
		MediaWikiServices $services, RoleManager $manager ) {
		parent::__construct( $config, $context, $services );
		$this->manager = $manager;
	}

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param MediaWikiServices $services
	 * @param RoleManager|null $manager
	 * @return IModule
	 */
	public static function getInstance( Config $config, IContextSource $context,
		MediaWikiServices $services, ?RoleManager $manager = null ) {
		if ( !$manager ) {
			$manager = $services->getService( 'BSRoleManager' );
		}
		return new static( $config, $context, $services, $manager );
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return bool
	 */
	public function applies( Title $title, User $user ) {
		if ( !$this->isAffected( $title ) ) {
			return false;
		}

		return $title->getNamespace() >= 0;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return bool
	 */
	public function mustLockdown( Title $title, User $user, $action ) {
		$actionRoles = $this->getActionRoles( $action );
		if ( empty( $actionRoles ) ) {
			return false;
		}
		// Does any of the roles containing this permission have a lockdown.
		$applies = false;
		$this->allowedGroups[$action] = [];
		foreach ( $this->config->get( 'NamespaceRolesLockdown' ) as $ns => $roles ) {
			if ( $ns !== $title->getNamespace() ) {
				continue;
			}
			foreach ( $roles as $roleName => $groups ) {
				$this->allowedGroups[$action] = array_merge(
					$this->allowedGroups[$action],
					$groups
				);
				if ( !in_array( $roleName, $actionRoles ) ) {
					continue;
				}
				// If any of the roles that are under lockdown are containing
				// permission we are testing for, lockdown applies
				$applies = true;
				if ( array_intersect( $groups, $this->getUserGroups( $user ) ) ) {
					return false;
				}
			}
		}

		if ( $applies === false ) {
			// Bail out if this permission is not affected by lockdown
			return false;
		}

		return true;
	}

	/**
	 * Returns the Message with the reason, this module was locked down.
	 * Should not be called befote mustLockdown() returns false
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return Message
	 */
	public function getLockdownReason( Title $title, User $user, $action ) {
		$allowedGroups = array_unique( $this->allowedGroups[$action] );
		return $this->msg( 'badaccess-groups', [
			$this->getContext()->getLanguage()->commaList( $allowedGroups ),
			count( $allowedGroups )
		] );
	}

	/**
	 *
	 * @param string $action
	 * @return array
	 */
	protected function getActionRoles( $action ) {
		$actionRoles = [];
		$roles = $this->getRoleManager()->getRoleNamesAndPermissions();
		foreach ( $roles as $role ) {
			if ( in_array( $action, $role['permissions'] ) ) {
				$actionRoles[] = $role['role'];
			}
		}
		return $actionRoles;
	}

	/**
	 *
	 * @return Manager
	 */
	protected function getRoleManager() {
		return $this->manager;
	}

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	protected function isAffected( Title $title ) {
		$affectedNamespaces = array_keys(
			$this->config->get( 'NamespaceRolesLockdown' )
		);
		// If there are no per-ns roles assigned for this ns don't block
		if ( !in_array( $title->getNamespace(), $affectedNamespaces ) ) {
			return false;
		}
		return true;
	}
}
