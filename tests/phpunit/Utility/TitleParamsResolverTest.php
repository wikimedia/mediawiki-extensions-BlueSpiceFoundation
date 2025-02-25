<?php

namespace BlueSpice\Tests\Utility;

use BlueSpice\Utility\TitleParamsResolver;
use MediaWiki\Title\Title;

/**
 * @group BlueSpice
 * @group BlueSpiceFoundation
 * @group medium
 * @group Database
 */
class TitleParamsResolverTest extends \MediaWikiIntegrationTestCase {

	protected function setUp(): void {
		parent::setUp();

		$this->insertPage( 'TitleParamsResolverTest' );
		$this->insertPage( 'Help:TitleParamsResolverTest' );
	}

	/**
	 *
	 * @param array $params
	 * @param string $expectedPrefixedText
	 * @param Title[] $defaultTitles
	 * @param string $message
	 * @dataProvider provideMediaWikiApiTitleOrPageIdData
	 * @covers \BlueSpice\Utility\TitleParamsResolver::resolve
	 */
	public function testMediaWikiApiTitleOrPageId( $params, $expectedPrefixedText,
		$defaultTitles, $message ) {
		$resolver = new TitleParamsResolver( $params, $defaultTitles );
		$resolvedTitles = $resolver->resolve();
		$resolvedTitle = $resolvedTitles[0];

		$this->assertTrue( $resolvedTitle instanceof Title );
		$this->assertEquals( $expectedPrefixedText, $resolvedTitle->getPrefixedText(), $message );
	}

	public function provideMediaWikiApiTitleOrPageIdData() {
		$mainPage = Title::newMainPage();
		$mainPageTitleText = $mainPage->getPrefixedText();

		return [ [
			[ 'pageid' => 1 ],
			'TitleParamsResolverTest',
			null,
			'Should resolve from "pageid"'
		], [
			[ 'title' => 'TitleParamsResolverTest' ],
			'TitleParamsResolverTest',
			null,
			'Should resolve from "title"'
		], [
			[ 'target' => 'TitleParamsResolverTest' ],
			'TitleParamsResolverTest',
			null,
			'Should resolve from "target"'
		], [
			[ 'page' => 'Help:TitleParamsResolverTest' ],
			'Help:TitleParamsResolverTest',
			null,
			'Should resolve from "page"'
		], [
			[ 'pageid' => -1 ],
			$mainPageTitleText,
			[ $mainPage ],
			'With invalid "page_id", should fallback to "default"'
		], [
			[ 'title' => 'Invalid[Title]' ],
			$mainPageTitleText,
			[ $mainPage ],
			'With invalid "title", should fallback to "default"'
		], [
			[ 'target' => 'Invalid[Title]' ],
			$mainPageTitleText,
			[ $mainPage ],
			'With invalid "target", should fallback to "default"'
		], [
			[ 'page' => 'Invalid[Title]' ],
			$mainPageTitleText,
			[ $mainPage ],
			'With invalid "page", should fallback to "default"'
		], [
			[ 'revid' => 1 ],
			'TitleParamsResolverTest',
			null,
			'Should resolve from "revid"'
		], [
			[ 'oldid' => 1 ],
			'TitleParamsResolverTest',
			null,
			'Should resolve from "oldid"'
		] ];
	}

	/**
	 *
	 * @param array $params
	 * @param string[] $expectedPrefixedTexts
	 * @param string $message
	 * @dataProvider provideMediaWikiApiTitlesOrPageIdsData
	 * @covers \BlueSpice\Utility\TitleParamsResolver::resolve
	 */
	public function testMediaWikiApiTitlesOrPageIds( $params, $expectedPrefixedTexts, $message ) {
		$resolver = new TitleParamsResolver( $params );
		$resolvedTitles = $resolver->resolve();
		$numResolvedTitles = count( $resolvedTitles );

		for ( $i = 0; $i < $numResolvedTitles; $i++ ) {
			$resolvedTitle = $resolvedTitles[$i];
			$expectedPrefixedText = $expectedPrefixedTexts[$i];

			$this->assertTrue( $resolvedTitle instanceof Title );
			$this->assertEquals( $expectedPrefixedText, $resolvedTitle->getPrefixedText(), $message );
		}
	}

	public function provideMediaWikiApiTitlesOrPageIdsData() {
		return [ [
			[ 'pageids' => '2' ],
			[ 'Help:TitleParamsResolverTest' ],
			'Should resolve from "pageids"'
		], [
			[ 'pageids' => '2|-1' ],
			[ 'Help:TitleParamsResolverTest' ],
			'Should resolve from "pageids" with invalid pageid'
		], [
			[ 'titles' => 'TitleParamsResolverTest|Help:TitleParamsResolverTest' ],
			[ 'TitleParamsResolverTest', 'Help:TitleParamsResolverTest' ],
			'Should resolve from "titles"'
		], ];
	}
}
