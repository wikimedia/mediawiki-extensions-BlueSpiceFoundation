<?php

namespace BlueSpice\Tests;


/**
 * @group BlueSpice
 */
class BSTasksApiSpecTest extends \MediaWikiTestCase {

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
	 * @expectedException MWException
	 */
	public function testUnsupportedTaskSpecException1() {
		new \BSTasksApiSpec( [
			'is a string' => 'is not an array'
		] );
	}

	/**
	 * @expectedException MWException
	 */
	public function testUnsupportedTaskSpecException2() {
		new \BSTasksApiSpec( [
			0 => [ 'key is int, but value is not array' ]
		] );
	}
}
