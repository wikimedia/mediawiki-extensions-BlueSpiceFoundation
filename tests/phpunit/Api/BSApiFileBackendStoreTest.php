<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiExtJSStoreTestBase;

/**
 * @group Broken
 * @group medium
 * @group api
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiFileBackendStoreTest extends BSApiExtJSStoreTestBase {
	protected $filenames;

	protected $iFixtureTotal = 3;

	protected function setUp() {
		parent::setUp();

		$this->filenames = [
			dirname( __DIR__ ) . "/data/images/demoImage.png",
			dirname( __DIR__  ). "/data/images/test.jpg",
			dirname( __DIR__ ). "/data/images/test.png",
		];

		foreach( $this->filenames as $filename ) {
			$status = \BsFileSystemHelper::uploadLocalFile( $filename );
		}
	}


	protected function createStoreFixtureData(){
		return 3;
	}

	protected function getModuleName() {
		return 'bs-filebackend-store';
	}

	protected function getStoreSchema() {
		return [
			'file_url' => [
				'type' => 'string'
			],
			'file_name' => [
				'type' => 'string'
			],
			'file_size' => [
				'type' => 'integer'
			],
			'file_bits' => [
				'type' => 'integer'
			],
			'file_user' => [
				'type' => 'integer'
			],
			'file_width' => [
				'type' => 'integer'
			],
			'file_height' => [
				'type' => 'integer'
			],
			'file_mimetype' => [
				'type' => 'string'
			],
			'file_user_text' => [
				'type' => 'string'
			],
			'file_user_display_text' => [
				'type' => 'string'
			],
			'file_user_link' => [
				'type' => 'string'
			],
			'file_extension' => [
				'type' => 'string'
			],
			'file_timestamp' => [
				'type' => 'string'
			],
			'file_mediatype' => [
				'type' => 'string'
			],
			'file_description' => [
				'type' => 'string'
			],
			'file_display_text' => [
				'type' => 'string'
			],
			'file_thumbnail_url' => [
				'type' => 'string'
			],
			'page_link' => [
				'type' => 'string'
			],
			'page_id' => [
				'type' => 'integer'
			],
			'page_title' => [
				'type' => 'string'
			],
			'page_prefixed_text' => [
				'type' => 'string'
			],
			'page_latest' => [
				'type' => 'integer'
			],
			'page_namespace' => [
				'type' => 'integer'
			],
			'page_categories' => [
				'type' => 'array'
			],
			'page_categories_links'=> [
				'type' => 'array'
			],
			'page_is_redirect' => [
				'type' => 'boolean'
			],
			'page_is_new' => [
				'type' => 'boolean'
			],
			'page_touched' => [
				'type' => 'string'
			]
		];
	}

	public function provideMultipleFilterData() {
		return [
			'Filter by file_name and file_extension' => [
				[
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'file_name',
						'value' => 'Test.png'
					],
					[
						'type' => 'string',
						'comparison' => 'eq',
						'field' => 'file_extension',
						'value' => 'png'
					]
				],
				1
			]
		];
	}

	public function provideSingleFilterData() {
		return [
			'Filter by file_mimetype' => [ 'string', 'eq', 'file_mimetype', 'image/png', 2 ]
		];
	}

}
