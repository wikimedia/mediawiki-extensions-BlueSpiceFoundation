<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiTasksTestBase;

/**
 * @group medium
 * @group API
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 *
 * Class BSApiWikiPageTasksTest
 */
class BSApiWikiPageTasksTest extends BSApiTasksTestBase {
	protected function getModuleName() {
		return 'bs-wikipage-tasks';
	}

	public function setUp() {
		parent::setUp();

		$this->insertPage( 'Category Test', '[[Category:Mouse]] [[Category:Bird]] [[Category:Pink unicorn]]' );
	}

	public function testAddCategoriesSucceeds() {
		$oTitle = \Title::newFromText( 'Category Test' );
		$aTestCategories = [
			'CatA' => \Title::newFromText( 'CatA', NS_CATEGORY )->getText(),
			'cAT_B' => \Title::newFromText( 'cAT_B', NS_CATEGORY )->getText(),
			'CAT C' => \Title::newFromText( 'CAT C', NS_CATEGORY )->getText(),
			'cat____d'=> \Title::newFromText( 'cat____d', NS_CATEGORY )->getText(),
			'CAT   f  g' => \Title::newFromText( 'CAT   f  g', NS_CATEGORY )->getText()
		];

		$response = $this->executeTask(
			'addCategories',
			[
				'page_id' => $oTitle->getArticleID(),
				'categories' => array_keys( $aTestCategories )
			]
		);

		$this->assertEquals( true, $response->success, "Adding categories failed where it should have succeeded" );

		$oWikiPage = \WikiPage::factory( $oTitle );
		$aCategoryTitles = $oWikiPage->getCategories();
		$aActualCategories = [];
		foreach( $aCategoryTitles as $oCategoryTitle ) {
			$aActualCategories[] = $oCategoryTitle->getText();
		}

		$aNormalizedTestCategories = array_values( $aTestCategories );
		sort( $aNormalizedTestCategories );
		$aIntersection = array_intersect( $aActualCategories, $aNormalizedTestCategories );
		sort( $aIntersection );
		$this->assertArrayEquals(
			$aIntersection,
			$aNormalizedTestCategories,
			'Not all categories were set'
		);
	}

	public function testSetCategoriesSucceeds() {
		$oTitle = \Title::newFromText( 'Category Test' );
		$aTestCategories = [
			'Cat' => \Title::newFromText( 'Cat', NS_CATEGORY )->getText(),
		];

		$response = $this->executeTask(
			'setCategories',
			[
				'page_id' => $oTitle->getArticleID(),
				'categories' => array_keys( $aTestCategories )
			]
		);

		$this->assertEquals( true, $response->success, "Setting categories failed where it should have succeeded" );

		$oWikiPage = \WikiPage::factory( $oTitle );
		$aCategoryTitles = $oWikiPage->getCategories();
		$aActualCategories = [];
		foreach( $aCategoryTitles as $oCategoryTitle ) {
			$aActualCategories[] = $oCategoryTitle->getText();
		}

		$aNormalizedTestCategories = array_values( $aTestCategories );

		sort( $aNormalizedTestCategories );
		sort( $aActualCategories );

		$this->assertArrayEquals(
			$aActualCategories,
			$aNormalizedTestCategories,
			'Not all categories were set'
		);
	}

	public function testRemoveCategoriesSucceeds() {
		$oTitle = \Title::newFromText( 'Category Test' );

		$response = $this->executeTask(
			'removeCategories',
			[
				'page_id' => $oTitle->getArticleID(),
				'categories' => [ 'Mouse' ]
			]
		);

		$this->assertEquals( true, $response->success, "Removing categories failed where it should have succeeded" );

		$oWikiPage = \WikiPage::factory( $oTitle );
		$aCategoryTitles = $oWikiPage->getCategories();
		$aActualCategories = [];
		foreach( $aCategoryTitles as $oCategoryTitle ) {
			$aActualCategories[] = $oCategoryTitle->getText();
		}

		sort( $aActualCategories );

		$this->assertFalse( in_array( 'Mouse', $aActualCategories ) );
	}
}
