<?php

namespace BlueSpice\Tests;

use MediaWiki\Tests\Api\ApiTestCase;
use MediaWiki\User\User;

class BSApiTestCase extends ApiTestCase {

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
			$users = static::$userFixtures->makeTestUsers();
			foreach ( $users as $idx => $user ) {
				self::$users->offsetSet( $idx, $user );
			}
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
