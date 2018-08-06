<?php

namespace BlueSpice\Tests;

use BlueSpice\Tests\BSPageFixturesProvider;

class BSPageFixtures {


	/**
	 *
	 * @param BSApiTestCase $testcase
	 * @param BSFixturesProvider|null $provider
	 */
	public function __construct( $testcase, $provider = null ) {
		if( $provider === null ) {
			$provider = new BSPageFixturesProvider();
		}

		foreach ( $provider->getFixtureData() as $pageData ) {
			$testcase->insertPage( $pageData[0], $pageData[1] );
		}
	}
}
