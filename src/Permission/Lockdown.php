<?php

namespace BlueSpice\Permission;

use BlueSpice\Permission\Lockdown\IModule;
use MediaWiki\Config\Config;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class Lockdown {
	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var IModule[]
	 */
	protected $modules = [];

	/**
	 *
	 * @var Status[]
	 */
	protected $lockStates = [];

	/**
	 *
	 * @var IModule[]
	 */
	protected $appliedModules = null;

	/**
	 *
	 * @param Config $config
	 * @param Title $title
	 * @param User $user
	 * @param IModule[] $modules
	 */
	public function __construct( Config $config, Title $title, User $user, array $modules = [] ) {
		$this->config = $config;
		$this->title = $title;
		$this->user = $user;
		$this->modules = $modules;
	}

	/**
	 * Returns if the given action is locked or not
	 * @param string $action
	 * @return bool
	 */
	public function isLockedDown( $action = 'read' ) {
		return !$this->getLockState( $action )->isOK();
	}

	/**
	 * This is where the magic happens ;)
	 * @param string $action
	 * @return Status
	 */
	public function getLockState( $action = 'read' ) {
		if ( $action !== 'read' ) {
			$readLockState = $this->getLockState();
			// always check read first. If this fails there should not be another
			// permission applied
			if ( !$readLockState->isOK() ) {
				return $readLockState;
			}
		}

		if ( isset( $this->lockStates[$action] ) ) {
			return $this->lockStates[$action];
		}
		$this->lockStates[$action] = Status::newGood();
		if ( $action === 'read' && $this->isWhitelisted() ) {
			return $this->lockStates[$action];
		}
		// don't impose extra restrictions on UI pages. Lockdown see:
		// https://github.com/wikimedia/mediawiki-extensions-Lockdown/blob/master/src/Hooks.php#L84-L87
		if ( $this->title->isUserConfigPage() ) {
			return $this->lockStates[$action];
		}
		foreach ( $this->getAppliedModules() as $module ) {
			// as soon as any of the applied modules locks down the given action
			// for given user and title relation, permission is denied and we can
			// just abort any further proccessing
			if ( !$module->mustLockdown( $this->title, $this->user, $action ) ) {
				continue;
			}

			$this->lockStates[$action]->fatal( $module->getLockdownReason(
				$this->title,
				$this->user,
				$action
			) );
			return $this->lockStates[$action];
		}
		return $this->lockStates[$action];
	}

	/**
	 * Collects the registered modules that apply to the current user and title
	 * relation
	 * @return IModule[]
	 */
	protected function getAppliedModules() {
		if ( $this->appliedModules !== null ) {
			return $this->appliedModules;
		}

		$this->appliedModules = [];
		foreach ( $this->getModules() as $module ) {
			if ( !$module->applies( $this->title, $this->user ) ) {
				continue;
			}
			$this->appliedModules[] = $module;
		}
		return $this->appliedModules;
	}

	/**
	 *
	 * @return IModules[]
	 */
	protected function getModules() {
		return $this->modules;
	}

	/**
	 *
	 * @return bool
	 */
	protected function isWhitelisted() {
		if ( !is_array( $this->config->get( 'WhitelistRead' ) ) ) {
			return false;
		}
		if ( !in_array( $this->title->getPrefixedText(), $this->config->get( 'WhitelistRead' ) ) ) {
			return false;
		}
		return true;
	}

}
