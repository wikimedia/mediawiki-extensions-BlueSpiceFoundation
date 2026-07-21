<?php

namespace BlueSpice\Utility;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\User;

/**
 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
 */
class MaintenanceUser {

	/** @var Config */
	protected $config;

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @return string
	 */
	public function getUserName() {
		return User::MAINTENANCE_SCRIPT_USER;
	}

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
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
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @param int $expireInSeconds deprecated, not used anymore
	 * @return User
	 */
	public function getUser( $expireInSeconds = 10 ) {
		return User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] );
	}

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
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
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @return array
	 */
	protected function getGroups() {
		return [ 'sysop', 'bureaucrat', 'bot' ];
	}

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @param User $user
	 * @param int|null $expiry deprecated, not used anymore
	 */
	protected function addGroups( User $user, $expiry ) {
		$userGroupManager = MediaWikiServices::getInstance()->getUserGroupManager();
		foreach ( $this->getGroups() as $group ) {
			if ( in_array( $group, $userGroupManager->getUserGroups( $user ) ) ) {
				continue;
			}
			$userGroupManager->addUserToGroup( $user, $group );
		}
	}

	/**
	 * @deprecated use User::newSystemUser( User::MAINTENANCE_SCRIPT_USER, [ 'steal' => true ] ) instead
	 * @param int $expireInSeconds deprecated, not used anymore
	 * @return string
	 */
	protected function getExpiryTS( $expireInSeconds ) {
		return '';
	}

}
