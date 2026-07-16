<?php

namespace BlueSpice\Hook\GetUserPermissionsErrors;

use BlueSpice\Permission\Lockdown;
use BlueSpice\PermissionLockdownFactory;
use MediaWiki\Message\Message;
use MediaWiki\Permissions\Hook\GetUserPermissionsErrorsHook;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class ApplyLockdown implements GetUserPermissionsErrorsHook {

	/** @var PermissionLockdownFactory */
	private PermissionLockdownFactory $lockdownFactory;

	/**
	 * @param PermissionLockdownFactory $lockdownFactory
	 */
	public function __construct( PermissionLockdownFactory $lockdownFactory ) {
		$this->lockdownFactory = $lockdownFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function onGetUserPermissionsErrors( $title, $user, $action, &$result ) {
		if ( $this->shouldSkip( $title, $user, $action ) ) {
			return true;
		}

		$lockdown = $this->getLockdown( $title, $user );
		$res = $lockdown->getLockState( $action );

		if ( !$res->isOK() ) {
			if ( is_string( $result ) ) {
				if ( empty( $result ) ) {
					$result = [];
				} else {
					$result = [ $result ];
				}
			}
			$errors = $res->getMessages( 'error' );
			foreach ( $errors as $error ) {
				$result[] = Message::newFromSpecifier( $error )->text();
			}
		}

		return false;
	}

	/**
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @return bool
	 */
	private function shouldSkip( Title $title, User $user, string $action ): bool {
		if ( MW_ENTRY_POINT === 'cli' && $action === 'read' ) {
			if ( $user->isSystemUser() || $user->getName() === '127.0.0.1' ) {
				return true;
			}
		}
		return !$this->getLockdown( $title, $user )->isLockedDown( $action );
	}

	/**
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown
	 */
	private function getLockdown( Title $title, User $user ): Lockdown {
		return $this->lockdownFactory->newFromTitleAndUserRelation( $title, $user );
	}

}
