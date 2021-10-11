<?php

namespace BlueSpice\Tests\Utility;

use BlueSpice\Utility\UrlTitleParser;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;

class UrlTitleParserTest extends MediaWikiIntegrationTestCase {

	protected function setUp(): void {
		parent::setUp();
		$this->setMwGlobals( 'wgServer', 'http://tollerserver.de' );
		$this->setMwGlobals( 'wgScriptPath', '/w' );
		$this->setMwGlobals( 'wgArticlePath', '/wiki/$1' );
	}

	/**
	 * @dataProvider provideFromUrlWithTitleData
	 * @covers \BlueSpice\Utility\UrlTitleParser::parseTitle
	 */
	public function testFromUrl( $url, $expectation ) {
		$parser = new UrlTitleParser(
			$url,
			MediaWikiServices::getInstance()->getMainConfig()
		);
		$title = $parser->parseTitle();

		$this->assertEquals( $expectation, $title->getFullText() );
	}

	/**
	 * @dataProvider provideFromUrlWithExceptionData
	 * @covers \BlueSpice\Utility\UrlTitleParser::parseTitle
	 */
	public function testFromUrlException( $url, $exception ) {
		$this->expectException( $exception );

		$parser = new UrlTitleParser(
			$url,
			MediaWikiServices::getInstance()->getMainConfig()
		);
		$title = $parser->parseTitle();
	}

	public function provideFromUrlWithExceptionData() {
		return [
			'normal-path-with-ending-slash' => [
				'http://tollerserver.de/', 'MWException'
			],
			'normal-path-without-ending-slash' => [
				'http://tollerserver.de', 'MWException'
			]
		];
	}

	public function provideFromUrlWithTitleData() {
		return [
			'normal-with-index' => [
				'http://tollerserver.de/index.php?title=Sometitle&do=something',
				'Sometitle'
			],
			'normal-without-index' => [
				'http://tollerserver.de/Some_title?a=b',
				'Some title'
			],
			'subpage-with-script-path' => [
				'http://tollerserver.de/w/index.php?title=Some_title/some/Sub',
				'Some title/some/Sub'
			],
			'prefixed-with-index' => [
				'http://tollerserver.de/index.php?title=Prefix%3ASome_title',
				'Prefix:Some title'
			],
			'prefixed-with-index-and-script-path' => [
				'http://tollerserver.de/w/index.php?title=Prefix%3ASome_title',
				'Prefix:Some title'
			],
			'prefixed-with-article-path' => [
				'http://tollerserver.de/wiki/Prefix%3ASome_title',
				'Prefix:Some title'
			],
			'normal-with-umlauts' => [
				'http://tollerserver.de/w/index.php/S%C3%B6me_title',
				'Söme title'
			],
			'subpage-with-umlauts' => [
				'http://tollerserver.de/wiki/Hallo/S%C3%B6me_title',
				'Hallo/Söme title'
			],
			'subpage-with-query' => [
				'http://tollerserver.de/wiki/Hallo/S%C3%B6me_title?debug=true&weil_so_schoen_is=nochwas',
				'Hallo/Söme title'
			],
			'non-existing-namespace' => [
				'http://tollerserver.de/SomeNS:A_Tollesache_das/Wenn_es_geht',
				'SomeNS:A Tollesache das/Wenn es geht'
			]
			// .. Umlauts, Namespace prefixes...
		];
	}
}
