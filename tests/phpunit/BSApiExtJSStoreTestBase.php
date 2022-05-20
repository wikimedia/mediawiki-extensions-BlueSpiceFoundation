<?php

namespace BlueSpice\Tests;

/**
 *
 * Class BSApiExtJSStoreTestBase
 */
abstract class BSApiExtJSStoreTestBase extends BSApiTestCase {

	protected $iFixtureTotal = 0;
	protected $sQuery = '';

	/**
	 * @return array
	 */
	abstract protected function getStoreSchema();

	/**
	 * @return int Total count of fixture data sets available to the test
	 */
	abstract protected function createStoreFixtureData();

	/**
	 * @return string
	 */
	abstract protected function getModuleName();

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
		foreach ( $schema as $schemaFieldName => $config ) {
			$this->assertObjectHasAttribute(
				$schemaFieldName,
				$firstRow,
				"Dataset misses field '$schemaFieldName'' from schema definition!"
			);
			$value = $firstRow->{$schemaFieldName};

			switch ( $config['type'] ) {
				case 'string':
					$this->assertTrue(

						is_string( $value ),
						"Value of field '$schemaFieldName' is not a string"
					);
					break;
				case 'list':
					$this->assertIsArray(

						$value,
						"Value of field '$schemaFieldName' is not a list"
					);
					break;
				case 'numeric':
					$this->assertTrue(

						is_numeric( $value ),
						"Value of field '$schemaFieldName' is not a number"
					);
					break;
				case 'boolean':
					$this->assertTrue(

						is_bool( $value ),
						"Value of field '$schemaFieldName' is not a boolean"
					);
					break;
				case 'date':
					$this->assertNotEquals(
						-1,
						strtotime( $value ),
						"Value of field '$schemaFieldName' is not a valid date format"
					);
					break;
				case 'title':
					$this->assertNotNull(
						\Title::newFromText( $value ),
						"Value of field '$schemaFieldName' is not a valid title"
					);
					break;
				case 'templatetitle':
					$this->assertNotNull(
						\Title::newFromText( $value, NS_TEMPLATE ),
						"Value of field '$schemaFieldName' is not a valid template title"
					);
					break;
			}
		}
	}

	/**
	 * @param mixed $keyItemKey The array key of an item to look for
	 * @param mixed $keyItemValue The array value of an item to look for
	 *
	 * @dataProvider provideKeyItemData
	 */
	public function testKeyItem( $keyItemKey, $keyItemValue ) {
		$aParams = [
			'action' => $this->getModuleName()
		];
		if ( $this->sQuery ) {
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
	 * @param int $limit
	 * @param int $offset
	 *
	 * @dataProvider providePagingData
	 */
	public function testPaging( $limit, $offset ) {
		$aParams = [
			'action' => $this->getModuleName(),
			'limit' => $limit,
			'start' => $offset
		];
		if ( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );
		$results = $this->doApiRequest( $aParams );
		$response = $results[0];

		if ( !$this->skipAssertTotal() ) {
			$this->assertSame(
				$this->iFixtureTotal,
				$response['total'],
				'Field "total" contains wrong value'
			);
		}

		$this->assertLessThanOrEqual(
			$limit,
			count( $response[ $this->getResultsNodeName() ] ),
			'Number of results exceeds limit'
		);
	}

	public function providePagingData() {
		return [
			[ 2, 0 ],
			[ 2, 2 ],
			[ 2, 4 ],
			[ 4, 0 ],
			[ 4, 4 ],
			[ 4, 8 ]
		];
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
	 * @param string $type
	 * @param string $comparison
	 * @param string $field
	 * @param mixed $value
	 * @param \stdClass $expectedTotal
	 *
	 * @dataProvider provideSingleFilterData
	 */
	public function testSingleFilter( $type, $comparison, $field, $value, $expectedTotal ) {
		$aParams = [
			'action' => $this->getModuleName(),
			'filter' => \FormatJson::encode( [
				[
					'type' => $type,
					'comparison' => $comparison,
					'field' => $field,
					'value' => $value
				]
			] )
		];
		if ( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		$results = $this->doApiRequest( $aParams );

		$response = $results[0];

		$this->assertSame(
			$expectedTotal,
			$response['total'],
			'Field "total" contains wrong value'
		);
	}

	abstract public function provideSingleFilterData();

	/**
	 * @param string $filters
	 * @param \stdClass $expectedTotal
	 *
	 * @dataProvider provideMultipleFilterData
	 */
	public function testMultipleFilter( $filters, $expectedTotal ) {
		$aParams = [
			'action' => $this->getModuleName(),
			'filter' => \FormatJson::encode( $filters )
		];
		if ( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		$results = $this->doApiRequest( $aParams );

		$response = $results[0];

		$this->assertSame(
			$expectedTotal,
			$response['total'],
			'Field "total" contains wrong value'
		);
	}

	abstract public function provideMultipleFilterData();

	protected function makeRequestParams() {
		$aParams = [
			'action' => $this->getModuleName()
		];
		if ( $this->sQuery ) {
			$aParams['query'] = $this->sQuery;
		}
		$aParams = array_merge( $aParams, $this->getAdditionalParams() );

		return $aParams;
	}

	/**
	 * Indicates whether the total count of a store should be tested
	 * @return bool
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
		foreach ( $haystack as $itemKey => $itemValue ) {
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
