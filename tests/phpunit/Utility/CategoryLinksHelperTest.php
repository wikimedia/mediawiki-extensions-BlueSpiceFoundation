<?php

namespace BlueSpice\Tests\Utility;

use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use PHPUnit\Framework\TestCase;
use Title;

class CategoryLinksHelperTest extends TestCase {

	/**
	 * Covers case when category is removed from page categories, but there also is semantic
	 * query with this category in page content.
	 *
	 * Expected behavior:
	 * Semantic query should be preserved without changes.
	 * But page's category should be changed.
	 *
	 * @covers \BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper::removeTarget()
	 */
	public function testRemoveCategorySimpleCase() {
		$origWikitext = "
{{#ask:[[Category:ABC]]}}
{{#ask2:  [[Category:ABC]]}}
{{#ask3: [[Category:ABC]]
|format=broadtable
}}

[[Category:ABC]]";
		$helper = new CategoryLinksHelper( $origWikitext );

		$categoryTitle = Title::makeTitle( NS_CATEGORY, 'ABC' );
		$helper->removeTargets( [ $categoryTitle ] );

		$categoryTitle = Title::makeTitle( NS_CATEGORY, 'DEF' );
		$helper->addTargets( [ $categoryTitle ] );

		$expectedWikitext = "
{{#ask:[[Category:ABC]]}}
{{#ask2:  [[Category:ABC]]}}
{{#ask3: [[Category:ABC]]
|format=broadtable
}}

[[Category:DEF]]";

		$actualWikitextClean = str_replace( "\n", '', $helper->getWikiText() );
		$expectedWikitextClean = str_replace( "\n", '', $expectedWikitext );

		$this->assertEquals( $expectedWikitextClean, $actualWikitextClean );
	}
}
