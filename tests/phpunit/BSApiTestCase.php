<?php

namespace BlueSpice\Tests;

use User;

class BSApiTestCase extends \ApiTestCase {

	/**
	 *
	 * @var BSUserFixtures
	 */
	protected static $userFixtures = null;

	/**
	 *
	 * @param BSUserFixtures $userFixtures
	 */
	public function setUserFixture( $userFixtures ) {
		static::$userFixtures = $userFixtures;
	}

	protected function setUp(): void {
		parent::setUp();

		if ( static::$userFixtures instanceof BSUserFixtures ) {
			self::$users += static::$userFixtures->makeTestUsers();
		}
	}

	/**
	 * Making this public so we can make use of it from within a "*Fixtures"
	 * class
	 * @param string $pageName
	 * @param string $text
	 * @param int|null $namespace
	 * @param User|null $user
	 * @return array
	 */
	public function insertPage( $pageName, $text = 'Sample page for unit test.',
		$namespace = null, User $user = null ) {
		return parent::insertPage( $pageName, $text, $namespace, $user );
	}
}
