<?php

namespace BlueSpice\Permission\Lockdown\Module;

use BlueSpice\ExtensionAttributeBasedRegistry;
use BlueSpice\Permission\Lockdown\IModule;
use BlueSpice\Permission\Lockdown\Module;
use BlueSpice\Permission\Lockdown\Module\Groups\ISubModule;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class Groups extends Module {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var string[]
	 */
	protected $lockdownGroups = null;

	/**
	 *
	 * @var ISubModule[]
	 */
	protected $subModule = null;

	/**
	 *
	 * @var ISubModule[]
	 */
	protected $appliedSubModules = null;

	/**
	 *
	 * @var Message[]
	 */
	protected $reasons = [];

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return bool
	 */
	public function applies( Title $title, User $user ) {
		if ( $title->getNamespace() < 0 ) {
			return false;
		}
		if ( empty( $this->getAppliedSubModules( $title, $user ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param MediaWikiServices $services
	 * @param ExtensionAttributeBasedRegistry $registry
	 */
	protected function __construct( Config $config, IContextSource $context,
		MediaWikiServices $services, ExtensionAttributeBasedRegistry $registry ) {
		parent::__construct( $config, $context, $services );
		$this->registry = $registry;
	}

	/**
	 *
	 * @param Config $config
	 * @param IContextSource $context
	 * @param Services $services
	 * @param ExtensionAttributeBasedRegistry|null $registry
	 * @return IModule
	 */
	public static function getInstance( Config $config,
		IContextSource $context, MediaWikiServices $services,
		?ExtensionAttributeBasedRegistry $registry = null ) {
		if ( !$registry ) {
			$registry = new ExtensionAttributeBasedRegistry(
				'BlueSpiceFoundationPermissionLockdownGroupModuleRegistry'
			);
		}
		return new static( $config, $context, $services, $registry );
	}

	/**
	 *
	 * @return ISubModules[]
	 */
	protected function getSubModules() {
		if ( $this->subModule !== null ) {
			return $this->subModule;
		}
		$this->subModule = [];
		foreach ( $this->registry->getAllKeys() as $key ) {
			$this->subModule[] = call_user_func_array( $this->registry->getValue( $key ), [
				$this->getConfig(),
				$this->getContext(),
				$this->getServices(),
			] );
		}
		return $this->subModule;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return bool
	 */
	public function mustLockdown( Title $title, User $user, $action ) {
		$groups = $this->getAppliedGroups( $title, $user, $action );
		if ( empty( $groups ) ) {
			return false;
		}

		foreach ( $groups as $group ) {
			if ( in_array( $group, $this->getLockdownGroups( $user ) ) ) {
				continue;
			}
			// if this action is in a non lockdownable group, we can not lock it
			// anyway, so just return
			return false;
		}
		$this->reasons[$action] = [];
		$lockGroups = [];
		foreach ( $this->getAppliedSubModules( $title, $user ) as $module ) {
			foreach ( $groups as $group ) {
				if ( !in_array( $group, $module->getLockdownGroups( $user ) ) ) {
					continue;
				}
				// collect all lockdowns from applied subModule for this action
				// for given user and title relation. To deny the permission it is
				// necessary for every module to lockdown
				$lockGroups[] = $group;
				if ( !$module->mustLockdown( $title, $user, $action ) ) {
					continue;
				}
				$this->reasons[$action][] = $module->getLockdownReason(
					$title,
					$user,
					$action
				);
			}
		}
		// if there is no reason at all, we dont need to lock down anything :)
		// i.e. whenever the user is not in one of the lockdown groups
		if ( empty( $this->reasons[$action] ) ) {
			return false;
		}
		// only set this to locked down when all of the lockdown groups get locked
		// as we mimic adding permissions
		if ( count( $this->reasons[$action] ) === count( $lockGroups ) ) {
			return true;
		}
		return false;
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
		$actionMsg = $this->msg( "right-$action" );
		return $this->msg( 'bs-lockdown-groups-permissionerror', [
			$actionMsg->exists() ? $actionMsg : $action,
			count( $this->reasons[$action] ),
			implode( ', ', $this->reasons[$action] ),
		] );
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param string[] $groups
	 * @return string[]
	 */
	protected function getAppliedGroups( Title $title, User $user, $action, array $groups = [] ) {
		$roleManager = MediaWikiServices::getInstance()->getService( 'BSRoleManager' );
		$roleManager instanceof \BlueSpice\Permission\Role\Manager;

		foreach ( $this->getUserGroups( $user ) as $group ) {
			foreach ( $this->config->get( 'GroupRoles' ) as $roleGroup => $roles ) {
				if ( $roleGroup !== $group ) {
					continue;
				}
				foreach ( $roles as $roleName => $value ) {
					if ( $value === false ) {
						continue;
					}
					$role = $roleManager->getRole( $roleName );
					if ( !$role ) {
						continue;
					}
					if ( !in_array( $action, $role->getPermissions() ) ) {
						continue;
					}

					$lockdownNs = $this->config->get( 'NamespaceRolesLockdown' );
					if ( isset( $lockdownNs[$title->getNamespace()][$roleName] ) ) {
						if ( !in_array( $group, $lockdownNs[$title->getNamespace()][$roleName] ) ) {
							continue;
						}
						$groups[] = $group;
						continue;
					}
					$groups[] = $group;
				}
			}
		}
		return array_unique( $groups );
	}

	/**
	 *
	 * @param User $user
	 * @param string[] $groups
	 * @return string[]
	 */
	protected function getLockdownGroups( User $user, array $groups = [] ) {
		if ( $this->lockdownGroups ) {
			return $this->lockdownGroups;
		}
		$this->lockdownGroups = [];
		foreach ( $this->getSubModules() as $module ) {
			$groups = array_merge( $groups, $module->getLockdownGroups( $user ) );
		}

		$this->lockdownGroups = array_intersect(
			$groups,
			$this->getUserGroups( $user )
		);

		return $this->lockdownGroups;
	}

	/**
	 * Collects the registered subsubModule that apply to the current user and title
	 * relation
	 * @param Title $title
	 * @param User $user
	 * @return ISubModule[]
	 */
	protected function getAppliedSubModules( Title $title, User $user ) {
		if ( $this->appliedSubModules ) {
			return $this->appliedSubModules;
		}

		$this->appliedSubModules = [];
		foreach ( $this->getSubModules() as $module ) {
			if ( !$module->applies( $title, $user ) ) {
				continue;
			}
			$this->appliedSubModules[] = $module;
		}
		return $this->appliedSubModules;
	}

}
