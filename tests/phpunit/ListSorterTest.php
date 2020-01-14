<?php

namespace BlueSpice\Tests;

use BlueSpice\IListPositionProvider;
use BlueSpice\ListSorter;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListSorterTest extends TestCase {

	/**
	 * @covers ListSorter::__construct
	 */
	public function testContructor() {
		$sorter = new ListSorter();

		$this->assertInstanceOf( "BlueSpice\\ListSorter", $sorter, "Should be able to construct" );
	}

	/**
	 * @covers ListSorter::sort
	 */
	public function testSortDefault() {
		$sorter = new ListSorter();

		$items = $this->makeTestItems();

		$sortedItems = $sorter->sort( $items );

		$this->assertNotInstanceOf( IListPositionProvider::class, $sortedItems[0] );
		$this->assertEquals( 10, $sortedItems[1]->getPosition() );
		$this->assertEquals( 100, $sortedItems[4]->getPosition() );
	}

	/**
	 * @covers ListSorter::sort
	 */
	public function testSortASC() {
		$sorter = new ListSorter();

		$items = $this->makeTestItems();

		$sortedItems = $sorter->sort( $items, ListSorter::ASC );

		$this->assertNotInstanceOf( IListPositionProvider::class, $sortedItems[0] );
		$this->assertEquals( 10, $sortedItems[1]->getPosition() );
		$this->assertEquals( 100, $sortedItems[4]->getPosition() );
	}

	/**
	 * @covers ListSorter::sort
	 */
	public function testSortDESC() {
		$sorter = new ListSorter();

		$items = $this->makeTestItems();

		$sortedItems = $sorter->sort( $items, ListSorter::DESC );

		$this->assertEquals( 100, $sortedItems[0]->getPosition() );
		$this->assertEquals( 10, $sortedItems[3]->getPosition() );
		$this->assertNotInstanceOf( IListPositionProvider::class, $sortedItems[4] );
	}

	private function makeTestItems() {
		$item0 = $this->createMock( IListPositionProvider::class );
		$item0->expects( $this->any() )
			->method( 'getPosition' )
			->willReturn( 50 );

		$item1 = $this->createMock( IListPositionProvider::class );
		$item1->expects( $this->any() )
			->method( 'getPosition' )
			->willReturn( 10 );

		$item2 = new stdClass();

		$item3 = $this->createMock( IListPositionProvider::class );
		$item3->expects( $this->any() )
			->method( 'getPosition' )
			->willReturn( 100 );

		$item4 = $this->createMock( IListPositionProvider::class );
		$item4->expects( $this->any() )
			->method( 'getPosition' )
			->willReturn( 70 );

		return [ $item0, $item1, $item2, $item3, $item4 ];
	}

}
