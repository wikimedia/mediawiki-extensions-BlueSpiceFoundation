<?php

namespace BlueSpice\Utility;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\User;

class MaintenanceUser {

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 *
	 * @return string
	 */
	public function getUserName() {
		return $this->config->get( 'MaintenanceUserName' );
	}

	/**
	 *
	 * @param User|null $user
	 * @return bool
	 */
	public function isMaintenanceUser( ?User $user = null ) {
		if ( !$user ) {
			return false;
		}
		return $user->getName() === $this->getUserName();
	}

	/**
	 * @param int $expireInSeconds - Expire the users groups after the next
	 * x seconds. min 10 seconds
	 * @return User
	 */
	public function getUser( $expireInSeconds = 10 ) {
		$user = User::newSystemUser(
			$this->getUserName(),
			$this->getOptions()
		);
		if ( !$user ) {
			throw new \MWException(
				"Maintenace user '{$this->getUserName()}' could not be created"
			);
		}

		$this->addGroups( $user, $this->getExpiryTS( $expireInSeconds ) );

		return $user;
	}

	/**
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			'validate' => 'valid',
			'create' => true,
			'steal' => true,
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function getGroups() {
		return [ 'sysop', 'bureaucrat', 'bot' ];
	}

	/**
	 *
	 * @param User $user
	 * @param int|null $expiry
	 */
	protected function addGroups( User $user, $expiry ) {
		// removed the group expiry feature for now, because this could end in
		// deadlocks:
		// Query: UPDATE `user_groups` SET ug_expiry = '20180813134139'
		// WHERE ug_user = '16' AND ug_group = 'sysop'
		// Function: UserGroupMembership::insert
		// Error: 1213 Deadlock found when trying to get lock; try restarting transaction (db)
		$expiry = null;

		$userGroupManager = MediaWikiServices::getInstance()->getUserGroupManager();
		foreach ( $this->getGroups() as $group ) {
			if ( in_array( $group, $userGroupManager->getUserGroups( $user ) ) ) {
				continue;
			}
			$userGroupManager->addUserToGroup( $user, $group, $expiry );
		}
	}

	/**
	 *
	 * @param int $expireInSeconds
	 * @return string
	 */
	protected function getExpiryTS( $expireInSeconds ) {
		$expireInSeconds = (int)$expireInSeconds;
		if ( empty( $expireInSeconds ) || $expireInSeconds < 10 ) {
			$expireInSeconds = 10;
		}
		return ( new \DateTime( '+' . $expireInSeconds . ' seconds' ) )
			->format( 'YmdHis' );
	}
}
