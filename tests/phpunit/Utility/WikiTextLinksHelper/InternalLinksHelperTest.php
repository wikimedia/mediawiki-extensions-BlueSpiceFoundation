<?php

namespace BlueSpice\Tests\Utility\WikiTextLinksHelper;

use BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper;
use MediaWikiIntegrationTestCase;
use Title;

class InternalLinksHelperTest extends MediaWikiIntegrationTestCase {

	/**
	 *
	 * @return array
	 */
	protected function getExpected() {
		return $this->provideInternalLinks()
			+ $this->provideInterwikiLinks()
			+ $this->provideFileLinks()
			+ $this->provideInterlanguageLinks()
			+ $this->provideInterwikiLinksBroken()
			+ $this->provideCategoryLinks();
	}

	/**
	 *
	 * @param string $wikitext
	 * @return InternalLinksHelper
	 */
	protected function getHelper( $wikitext ) {
		return new InternalLinksHelper( $wikitext );
	}

	/**
	 * @covers BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper::getTargets
	 */
	public function testGetTargetMatches() {
		// Inspired by `ExtraParsertest::testParseLinkParameter`
		$this->setTemporaryHook( 'InterwikiLoadPrefix', function ( $prefix, &$iwData ) {
			if ( $prefix === 'nonexistent' ) {
				return true;
			}

			$iwData = [
				'iw_url' => 'http://doesnt.matter.org/$1',
				'iw_api' => '',
				'iw_wikiid' => '',
				'iw_local' => 0
			];

			$languagePrefixes = [ 'en', 'de' ];
			if ( in_array( $prefix, $languagePrefixes ) ) {
				$iwData['iw_local'] = 1;
			}

			return true;
		} );

		$wikitext = $this->provideWikitextData();
		$helper = $this->getHelper( $wikitext );
		foreach ( $this->getExpected() as $key => $title ) {
			$this->assertArrayHasKey( $key, $helper->getTargets() );
		}
	}

	/**
	 * @covers BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper::getTargets
	 */
	public function testGetTargetMatchCount() {
		$wikitext = $this->provideWikitextData();
		$helper = $this->getHelper( $wikitext );
		$this->assertEquals(
			count( $this->getExpected() ),
			count( $helper->getTargets() )
		);
	}

	/**
	 * @covers BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper::getTargets
	 */
	public function testGetTargets() {
		$wikitext = $this->provideWikitextData();
		$targets = ( $this->getHelper( $wikitext ) )->getTargets();
		foreach ( $this->getExpected() as $key => $title ) {
			$this->assertArrayHasKey( $key, $targets );

			$tartgetText = $targets[$key];
			$this->assertTrue(
				$title->equals( $tartgetText ),
				"\"{$title->getFullText()}\" not equal to \"{$targets[$key]->getFullText()}\"" );
		}
	}

	/**
	 *
	 * @return array
	 */
	protected function provideInternalLinks() {
		return [
			'[[Main_page]]' => Title::newFromText( 'Main_page' ),
			'[[Test|Testpage]]' => Title::newFromText( 'Test' ),
			'[[Project:Test|Testpage in NS]]' => Title::newFromText( 'Project:Test' ),
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideFileLinks() {
		return [
			'[[File:Test.jpg|MyFile]]' => Title::newFromText( 'File:Test.jpg' ),
			'[[Media:Test.png]]' => Title::newFromText( 'Media:Test.png' ),
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideInterwikiLinks() {
		return [
			'[[mw:Main_page]]' => Title::newFromText( 'mw:Main_page' ),
			'[[wiktionary:percussive_maintenance|Percussive Maintenance]]'
				=> Title::newFromText( 'wiktionary:percussive_maintenance' ),
			'[[wikipedia:BlueSpice_MediaWiki|BlueSpice]]'
				=> Title::newFromText( 'wikipedia:BlueSpice_MediaWiki' ),
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideInterwikiLinksBroken() {
		return [
			'[[nonexistent:Main_page]]' => Title::newFromText( 'nonexistent:Main_page' ),
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideInterlanguageLinks() {
		return [
			'[[en:Main_page]]' => Title::newFromText( 'en:Main_page' ),
			'[[de:Hauptseite|Start]]' => Title::newFromText( 'de:Hauptseite' ),
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideExternalLinks() {
		return [
			'[https://bluspice.com]' => null,
			'[https://hallowelt.com HalloWelt GmbH!]' => null
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function provideCategoryLinks() {
		return [
			'[[Category:Test]]' => Title::newFromText( 'Category:Test' ),
			'[[Category:Percussive_maintenance|Percussive Maintenance]]'
				=> Title::newFromText( 'Category:Percussive_maintenance' ),
		];
	}

	/**
	 *
	 * @return string
	 */
	protected function provideWikitextData() {
		$internal = implode( "\n", array_keys( $this->provideInternalLinks() ) );
		$files = implode( "\n", array_keys( $this->provideFileLinks() ) );
		$external = implode( "\n", array_keys( $this->provideExternalLinks() ) );
		$interwiki = implode( "\n", array_keys( $this->provideInterwikiLinks() ) );
		$interwikiBroken = implode( "\n", array_keys( $this->provideInterwikiLinksBroken() ) );
		$interlanugage = implode( "\n", array_keys( $this->provideInterlanguageLinks() ) );
		$category = implode( "\n", array_keys( $this->provideCategoryLinks() ) );
		return <<<HERE
==Internal Links==
$internal
===Files===
$files

==External Links==
$external

==Interwiki Links==
$interwiki
===Broken===
$interwikiBroken

==Interlanguage Links==
$interlanugage
==Categories==
$category

HERE;
	}

}
