<?php

namespace BlueSpice\Tests;

use MWException;

/**
 * @group BlueSpice
 */
class BSTasksApiSpecTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @covers \BSTasksApiSpec::getTaskNames
	 */
	public function testGetTaskNames() {
		$aTasks = [ 'test1', 'test2', 'test3' ];
		$oSpec = new \BSTasksApiSpec( $aTasks );
		$this->assertArrayEquals( $aTasks, $oSpec->getTaskNames() );

		$aTasks2 = [
			'test1' => [],
			'test2' => [],
			'test3' => [],
		];
		$oSpec2 = new \BSTasksApiSpec( $aTasks2 );
		$this->assertArrayEquals( $aTasks, $oSpec2->getTaskNames() );

		$aTasks3 = [
			'test1' => [],
			'test2',
			'test3' => [],
		];

		$oSpec3 = new \BSTasksApiSpec( $aTasks3 );
		$this->assertArrayEquals( $aTasks, $oSpec3->getTaskNames() );
	}

	/**
	 * @covers \BSTasksApiSpec::__construct
	 */
	public function testUnsupportedTaskSpecException1() {
		$this->expectException( MWException::class );
		new \BSTasksApiSpec( [
			'is a string' => 'is not an array'
		] );
	}

	/**
	 * @covers \BSTasksApiSpec::__construct
	 */
	public function testUnsupportedTaskSpecException2() {
		$this->expectException( MWException::class );
		new \BSTasksApiSpec( [
			0 => [ 'key is int, but value is not array' ]
		] );
	}
}
