<?php

namespace BlueSpice\Tests;

use BlueSpice\Tests\BSApiTestCase;
/**
 *
 * Class BSApiExtJSStoreTestBase
 */
abstract class BSApiExtJSStoreTestBase extends BSApiTestCase {

	protected $iFixtureTotal = 0;
	protected $sQuery = '';
	abstract protected function getStoreSchema();

	/**
	 * @return int Total count of fixture data sets available to the test
	 */
	abstract protected function createStoreFixtureData();
	abstract protected function getModuleName();

	protected function setUp() {
		parent::setUp();

		$this->doLogin();
	}

	public function addDBDataOnce() {
		// Caution: this is only called once per testsuite, not per test.
		$this->createStoreFixtureData();
	}

	public function testSchema() {
		$results = $this->doApiRequest(
			$this->makeRequestParams()
		);

		$response = $results[0];
		$firstRow = (object)$response[ $this->getResultsNodeName() ][0];
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
					$this->assertNotEquals( -1, strtotime( $value ), "Value of field '$schemaFieldName' is not a valid date format" );
					break;
				case 'title':
					$this->assertNotNull( \Title::newFromText( $value ), "Value of field '$schemaFieldName' is not a valid title" );
					break;
				case 'templatetitle':
					$this->assertNotNull( \Title::newFromText( $value, NS_TEMPLATE ), "Value of field '$schemaFieldName' is not a valid template title" );
					break;
			}
		}
	}

	/**
	 * @param $keyItemKey The array key of an item to look for
	 * @param $keyItemValue The array value of an item to look for
	 *
	 * @dataProvider provideKeyItemData
	 */
	public function testKeyItem( $keyItemKey, $keyItemValue ) {
		$aParams = array(
			'action' => $this->getModuleName()
		);
		if( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		$results = $this->doApiRequest( $aParams );
		$resultItems = $results[0][ $this->getResultsNodeName() ];

		$keyPresent = $this->array_findNestedKeyValuePair( $resultItems, $keyItemKey, $keyItemValue );

		$this->assertTrue( $keyPresent, "Key value pair not found in results" );
	}

	public function provideKeyItemData() {
		$this->markTestSkipped( "No key items to test" );
	}

	/**
	 * @param $limit
	 * @param $offset
	 *
	 * @dataProvider providePagingData
	 */
	public function testPaging( $limit, $offset ) {
		$aParams = array(
			'action' => $this->getModuleName(),
			'limit' => $limit,
			'start' => $offset
		);
		if( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );
		$results = $this->doApiRequest( $aParams );
		$response = $results[0];

		if ( !$this->skipAssertTotal() ) {
			$this->assertAttributeEquals(
				$this->iFixtureTotal,
				'total',
				(object)$response,
				'Field "total" contains wrong value'
			);
		}

		$this->assertLessThanOrEqual( $limit, count($response[ $this->getResultsNodeName() ]), 'Number of results exceeds limit' );
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
		$aParams = array(
			'action' => $this->getModuleName(),
			'filter' => \FormatJson::encode([
				[
					'type' => $type,
					'comparison' => $comparison,
					'field' => $field,
					'value' => $value
				]
			])
		);
		if( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		$results = $this->doApiRequest( $aParams );

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
		$aParams = array(
			'action' => $this->getModuleName(),
			'filter' => \FormatJson::encode( $filters )
		);
		if( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		$results = $this->doApiRequest( $aParams );

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
		$aParams = array(
			'action' => $this->getModuleName()
		);
		if( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		return $aParams;
	}

	/**
	 * Indicates whether the total count of a store should be tested
	 * @return boolean
	 */
	protected function skipAssertTotal() {
		return false;
	}

	/**
	 * Finds a key value pair in a multidimensional array
	 * @param array $haystack
	 * @param mixed $key
	 * @param mixed $value
	 * @return bool true if the pair was found
	 */
	protected function array_findNestedKeyValuePair( $haystack, $key, $value ) {
		foreach( $haystack as $itemKey => $itemValue ) {
			if ( $itemKey == $key && $itemValue == $value ) {
				return true;
			}
			if ( is_array( $itemValue ) ) {
				$foundInNested = $this->array_findNestedKeyValuePair( $itemValue, $key, $value );
				if ( $foundInNested === true ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Allows subclasses to define name of
	 * root node in results
	 * @return string
	 */
	protected function getResultsNodeName() {
		return 'results';
	}

	/**
	 * Allows subclasses to add custom parameters
	 * to the API calls
	 * @return array
	 */
	protected function getAdditionalParams() {
		return [];
	}

}
