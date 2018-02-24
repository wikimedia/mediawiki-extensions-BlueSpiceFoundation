<?php

namespace BlueSpice\Tests\Utility;

use BlueSpice\Utility\TitleParamsResolver;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @group medium
 * @group Database
 */
class TitleParamsResolverTest extends \MediaWikiTestCase {

	protected function setUp() {
		parent::setUp();
		$this->tablesUsed += [ 'page', 'revision', 'text' ];

		$this->insertPage( 'TitleParamsResolverTest' );
		$this->insertPage( 'Help:TitleParamsResolverTest' );
	}

	/**
	 *
	 * @param array $params
	 * @param string $expectedPrefixedText
	 * @param \Title $defaultTitle
	 * @param string $message
	 * @dataProvider provideMediaWikiApiTitleOrPageIdData
	 */
	public function testMediaWikiApiTitleOrPageId( $params, $expectedPrefixedText, $defaultTitle, $message ) {
		$resolver = new TitleParamsResolver( $params, $defaultTitle );
		$resolvedTitles = $resolver->resolve();
		$resolvedTitle = $resolvedTitles[0];

		$this->assertTrue( $resolvedTitle instanceof \Title );
		$this->assertEquals( $expectedPrefixedText, $resolvedTitle->getPrefixedText(), $message );
	}

	public function provideMediaWikiApiTitleOrPageIdData() {
		$mainPage = \Title::newMainPage();
		$mainPageTitleText = $mainPage->getPrefixedText();
		/**
		 * HINT: For all "page_id" and "revision_id" related test we need to
		 * rely on MediaWiki's build in "UTPage", because we can not determine
		 * the "page_id"/"revision_id" of a page that we create in "setUp" as
		 * they depend on the CI environment. Unfortunately PHPUnit runs the
		 * provider methods before "setUp" so we also can not use the id
		 * returned by "$this-insertPage".
		 */
		return [
			[ [ 'pageid' => 1 ], 'UTPage', null, 'Should resolve from "pageid"' ],
			[ [ 'title' => 'TitleParamsResolverTest' ], 'TitleParamsResolverTest', null, 'Should resolve from "title"' ],
			[ [ 'target' => 'TitleParamsResolverTest' ], 'TitleParamsResolverTest', null, 'Should resolve from "target"' ],
			[ [ 'page' => 'Help:TitleParamsResolverTest' ], 'Help:TitleParamsResolverTest', null, 'Should resolve from "page"' ],
			[ [ 'pageid' => -1 ], $mainPageTitleText, $mainPage, 'With invalid "page_id", should fallback to "default"' ],
			[ [ 'title' => 'Invalid[Title]' ], $mainPageTitleText, $mainPage, 'With invalid "title", should fallback to "default"' ],
			[ [ 'target' => 'Invalid[Title]' ], $mainPageTitleText, $mainPage, 'With invalid "target", should fallback to "default"' ],
			[ [ 'page' => 'Invalid[Title]' ], $mainPageTitleText, $mainPage, 'With invalid "page", should fallback to "default"' ],
			[ [ 'revid' => 1 ], 'UTPage', null, 'Should resolve from "revid"' ],
			[ [ 'oldid' => 1 ], 'UTPage', null, 'Should resolve from "oldid"' ]
		];
	}

	/**
	 *
	 * @param array $params
	 * @param string[] $expectedPrefixedTexts
	 * @param string $message
	 * @dataProvider provideMediaWikiApiTitlesOrPageIdsData
	 */
	public function testMediaWikiApiTitlesOrPageIds( $params, $expectedPrefixedTexts, $message ) {
		$resolver = new TitleParamsResolver( $params );
		$resolvedTitles = $resolver->resolve();

		for( $i = 0; $i < count( $resolvedTitles ); $i++ ) {
			$resolvedTitle = $resolvedTitles[$i];
			$expectedPrefixedText = $expectedPrefixedTexts[$i];

			$this->assertTrue( $resolvedTitle instanceof \Title );
			$this->assertEquals( $expectedPrefixedText, $resolvedTitle->getPrefixedText(), $message );
		}
	}

	public function provideMediaWikiApiTitlesOrPageIdsData() {
		return [
			[ [ 'pageids' => '1|2' ], [ 'UTPage', 'TitleParamsResolverTest'], 'Should resolve from "pageids"' ],
			[ [ 'pageids' => '2|-1' ], [ 'TitleParamsResolverTest'], 'Should resolve from "pageids" with invalid pageid' ],
			[ [ 'titles' => 'UTPage|Help:TitleParamsResolverTest' ], [ 'UTPage', 'Help:TitleParamsResolverTest'], 'Should resolve from "titles"' ],
		];
	}
}
