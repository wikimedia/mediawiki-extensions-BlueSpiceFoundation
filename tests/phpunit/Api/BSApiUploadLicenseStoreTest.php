<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiUploadLicenseStoreTest extends BSApiExtJSStoreTestBase {
	protected $iFixtureTotal = 1;

	protected function getStoreSchema() {
		return [
			'text' => [
				'type' => 'string'
			],
			'value' => [
				'type' => 'string'
			],
			'indent' => [
				'type' => 'integer'
			]
		];
	}

	protected function createStoreFixtureData() {
		/*$oLicensesTitle = \Title::makeTitle( NS_MEDIAWIKI, 'Licenses' );
		$oWikiPage = \WikiPage::factory( $oLicensesTitle );

		$sLicenses = "* Dummy | Dummy license\n* Dummy2 | Dummy2 license";
		$oUser = \User::newFromName( 'UTSysop' );
		$oContent = \ContentHandler::makeContent( $sLicenses, $oLicensesTitle );
		$oWikiPage->doEditContent( $oContent, '', 0, false, $oUser );*/

		return 1;
	}

	protected function getModuleName() {
		return 'bs-upload-license-store';
	}

	public function provideSingleFilterData() {
		return [
			'Filter by text' => [ 'string', 'ct', 'text', 'None', 1 ]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by indent and text' => [
				[
					[
						'type' => 'string',
						'comparison' => 'ct',
						'field' => 'text',
						'value' => 'none'
					],
					[
						'type' => 'numeric',
						'comparison' => 'eq',
						'field' => 'indent',
						'value' => 0
					]
				],
				1
			]
		];
	}

	public function provideKeyItemData() {
		return array(
			[ 'text', 'None selected' ]
		);
	}
}
