<?php

namespace BlueSpice\Tests\Utility;

use MediaWiki\MediaWikiServices;
use MediaWikiTestCase;
use BlueSpice\Utility\UrlTitleParser;

class UrlTitleParserTest extends MediaWikiTestCase {

	protected function setUp() {
		parent::setUp();
		$this->setMwGlobals( 'wgServer', 'http://tollerserver.de' );
		$this->setMwGlobals( 'wgScriptPath', '/' );
	}

	/**
	 * @param $url
	 * @param $expectation
	 * @dataProvider provideFromUrlWithTitleData
	 */
	public function testFromUrl( $url, $expectation ) {
		$parser = new UrlTitleParser( $url, MediaWikiServices::getInstance()->getMainConfig() );
		$title = $parser->parseTitle();

		$this->assertEquals( $expectation, $title->getFullText() );
	}

	/**
	 * @param $url
	 * @param $exception
	 * @dataProvider provideFromUrlWithExceptionData
	 */
	public function testFromUrlException( $url, $exception ) {
		$this->setExpectedException( $exception );

		$parser = new UrlTitleParser( $url, MediaWikiServices::getInstance()->getMainConfig() );
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
				'http://tollerserver.de/index.php?title=Sometitle&do=something', 'Sometitle'
			],
			'normal-without-index' => [
				'http://tollerserver.de/Some_title?a=b', 'Some title'
			],
			'subpage-without-index' => [
				'http://tollerserver.de/Some_title/some/Sub', 'Some title/some/Sub'
			],
			'prefixed-with-index' => [
				'http://tollerserver.de/index.php?title=Prefix%3ASome_title', 'Prefix:Some title'
			],
			'prefixed-without-index' => [
				'http://tollerserver.de/Prefix%3ASome_title', 'Prefix:Some title'
			],
			'normal-with-umlauts' => [
				'http://tollerserver.de/S%C3%B6me_title', 'Söme title'
			],
			'subpage-with-umlauts' => [
				'http://tollerserver.de/Hallo/S%C3%B6me_title', 'Hallo/Söme title'
			],
			'subpage-with-query' => [
				'http://tollerserver.de/Hallo/S%C3%B6me_title?debug=true&weil_so_schoen_is=nochwas', 'Hallo/Söme title'
			],
			'non-existing-namespace' => [
				'http://tollerserver.de/SomeNS:A_Tollesache_das/Wenn_es_geht', 'SomeNS:A Tollesache das/Wenn es geht'
			]
			//.. Umlauts, Namespace prefixes...
		];
	}
}
