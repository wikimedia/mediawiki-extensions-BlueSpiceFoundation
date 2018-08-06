<?php

namespace BlueSpice\Tests;

use BlueSpice\Tests\BSUserFixturesProvider;

class BSUserFixtures {

	/**
	 *
	 * @var array[]
	 */
	protected $fixtureData = [];

	/**
	 *
	 * @param BSApiTestCase $testcase
	 * @param BSFixturesProvider|null $provider
	 */
	public function __construct( $testcase, $provider = null ) {
		if( $provider === null ) {
			$provider = new BSUserFixturesProvider();
		}

		//Register at testcase so 'makeTestUsers' can be called in each and
		//every run of "setUp"
		$testcase->setUserFixture( $this );

		//Read in only once!
		$this->fixtureData = $provider->getFixtureData();
	}

	/**
	 *
	 * @return \TestUser[]
	 */
	public function makeTestUsers() {
		$users = [];
		foreach( $this->fixtureData as $userData ) {
			$user = new \TestUser(
				$userData[0], $userData[1], $userData[2], $userData[3]
			);
			$key = $this->makeKey( $userData[0] );

			$users[$key] = $user;
		}

		return $users;
	}

	protected function makeKey( $userName ) {
		return "bs-user-".strtolower( $userName );
	}

}
