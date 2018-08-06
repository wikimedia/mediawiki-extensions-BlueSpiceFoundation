<?php


namespace BlueSpice\Utility;

class MaintenanceUser {

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param \Config $config
	 */
	public function __construct( \Config $config ) {
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
	 * @param \User|null $user
	 * @return boolean
	 */
	public function isMaintenanceUser( \User $user = null ) {
		if( !$user ) {
			return false;
		}
		return $user->getName() === $this->getUserName();
	}

	/**
	 * @param integer $expireInSeconds - Expire the users groups after the next
	 * x seconds. min 10 seconds
	 * @return \User
	 */
	public function getUser( $expireInSeconds = 10 ) {

		$user = \User::newSystemUser(
			$this->getUserName(),
			$this->getOptions()
		);
		if( !$user ) {
			throw new \MWException(
				"Maintenace user '{$this->getUserName()}' could not be created"
			);
		}

		$this->addGroups( $user, $this->getExpiryTS( $expireInSeconds ) );

		return $user;
	}

	protected function getOptions() {
		return [
			'validate' => 'valid',
			'create' => true,
			'steal' => true,
		];
	}

	protected function getGroups() {
		return [ 'sysop', 'bureaucrat', 'bot' ];
	}

	protected function addGroups( \User $user, $expiry ) {
		foreach( $this->getGroups() as $group ) {
			$user->addGroup( $group, $expiry );
		}
	}

	protected function getExpiryTS( $expireInSeconds ) {
		$expireInSeconds = (int) $expireInSeconds;
		if( empty( $expireInSeconds ) || $expireInSeconds < 10 ) {
			$expireInSeconds = 10;
		}
		return ( new \DateTime( '+'.$expireInSeconds.' seconds' ) )
			->format( 'YmdHis' );
	}
}
