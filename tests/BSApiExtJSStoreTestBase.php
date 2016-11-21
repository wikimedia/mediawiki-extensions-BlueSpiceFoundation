<?php

/**
 *
 * Class BSApiExtJSStoreTestBase
 */
abstract class BSApiExtJSStoreTestBase extends ApiTestCase {

	protected $iFixtureTotal = 0;
	abstract protected function getStoreSchema();
	abstract protected function createStoreFixtureData();
	abstract protected function getModuleName();

	protected function setUp() {
		parent::setUp();

		$this->doLogin();
	}

	public function addDBDataOnce() {
		$this->createStoreFixtureData();
	}

	public function testSchema() {
		$results = $this->doApiRequest(
			$this->makeRequestParams()
		);

		$response = $results[0];
		$firstRow = (object)$response['results'][0];
		$schema = $this->getStoreSchema();
		foreach( $schema as $schemaFieldName => $config ) {
			$this->assertObjectHasAttribute( $schemaFieldName, $firstRow, "Dataset misses field '$schemaFieldName'' from schema definition!" );
			$value = $firstRow->{$schemaFieldName};

			switch( $config['type'] ) {
				case 'string':
					$this->assertEquals( true, is_string( $value ), "Value of field '$schemaFieldName' is not a string" );
					break;
				case 'list':
					$this->assertEquals( true, is_array( $value ), "Value of field '$schemaFieldName' is not a list" );
					break;
				case 'numeric':
					$this->assertEquals( true, is_numeric( $value ), "Value of field '$schemaFieldName' is not a number" );
					break;
				case 'boolean':
					$this->assertEquals( true, is_bool( $value ), "Value of field '$schemaFieldName' is not a boolean" );
					break;
				case 'date':
					$this->assertNotEquals( -1, strtotime(), "Value of field '$schemaFieldName' is not a valid date format" );
					break;
				case 'title':
					$this->assertNotNull( Title::newFromText( $value ), "Value of field '$schemaFieldName' is not a valid title" );
					break;
				case 'templatetitle':
					$this->assertNotNull( Title::newFromText( $value, NS_TEMPLATE ), "Value of field '$schemaFieldName' is not a valid template title" );
					break;
			}
		}
	}

	/**
	 * @param $limit
	 * @param $offset
	 *
	 * @dataProvider providePagingData
	 */
	public function testPaging( $limit, $offset ) {
		$results = $this->doApiRequest([
			'action' => $this->getModuleName(),
			'limit' => $limit,
			'offset' => $offset
		]);
		$response = $results[0];

		$this->assertAttributeEquals(
			$this->iFixtureTotal,
			'total',
			(object)$response,
			'Field "total" contains wrong value'
		);

		$this->assertLessThanOrEqual( $limit, count($response['results']), 'Number of results exceeds limit' );
	}

	public function providePagingData() {
		return array(
			[ 2, 0 ],
			[ 2, 2 ],
			[ 2, 4 ],
			[ 4, 0 ],
			[ 4, 4 ],
			[ 4, 8 ]
		);
	}

	/**
	 * [
	 * {
	 * "type":"string",
	 * "comparison":"ct",
	 * "value":"some text ...",
	 * "field":"someField"
	 * }
	 * ]
	 *
	 * @param $type
	 * @param $field
	 * @param $value
	 * @param $comparison
	 * @param $expectedTotal
	 *
	 * @dataProvider provideSingleFilterData
	 */
	public function testSingleFilter( $type, $comparison, $field, $value, $expectedTotal ) {
		$results = $this->doApiRequest([
			'action' => $this->getModuleName(),
			'filter' => FormatJson::encode([
				[
					'type' => $type,
					'comparison' => $comparison,
					'field' => $field,
					'value' => $value
				]
			])
		]);

		$response = $results[0];

		$this->assertAttributeEquals(
			$expectedTotal,
			'total',
			(object)$response,
			'Field "total" contains wrong value'
		);
	}

	abstract public function provideSingleFilterData();

	/**
	 * @param $filters
	 * @param $expectedTotal
	 *
	 * @dataProvider provideMultipleFilterData
	 */
	public function testMultipleFilter( $filters, $expectedTotal ) {
		$results = $this->doApiRequest([
			'action' => $this->getModuleName(),
			'filter' => FormatJson::encode( $filters )
		]);

		$response = $results[0];

		$this->assertAttributeEquals(
			$expectedTotal,
			'total',
			(object)$response,
			'Field "total" contains wrong value'
		);
	}

	abstract public function provideMultipleFilterData();

	protected function makeRequestParams() {
		return [
			'action' => $this->getModuleName()
		];
	}
}