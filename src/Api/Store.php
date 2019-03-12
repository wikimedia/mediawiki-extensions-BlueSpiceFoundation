<?php

namespace BlueSpice\Api;

use WikiPage;
use BlueSpice\Api;
use BlueSpice\Data\RecordConverter;
use BlueSpice\Data\ReaderParams;

/**
 * Example request parameters of an ExtJS store

	_dc:1430126252980
	filter:[
		{
			"type":"string",
			"comparison":"ct",
			"value":"some text ...",
			"field":"someField"
		}
	]
	group:[
		{
			"property":"someOtherField",
			"direction":"ASC"
		}
	]
	sort:[
		{
			"property":"someOtherField",
			"direction":"ASC"
		}
	]
	page:1
	start:0
	limit:25
 */
abstract class Store extends Api {

	/**
	 * May be overwritten by subclass
	 * @var string
	 */
	protected $root = 'results';

	/**
	 * May be overwritten by subclass
	 * @var string
	 */
	protected $totalProperty = 'total';

	/**
	 * May be overwritten by subclass
	 * @var string
	 */
	protected $metaData = 'metadata';

	/**
	 * Main method called by \ApiMain
	 */
	public function execute() {
		$dataStore = $this->makeDataStore();
		$result = $dataStore->getReader()->read( $this->getReaderParams() );
		$schema = $dataStore->getReader()->getSchema();
		$this->returnData( $result, $schema );
	}

	/**
	 * Creates a proper output format based on the class's properties
	 * @param \BlueSpice\Data\ResultSet $resultSet Holds the records as an
	 * array of plain old data objects
	 * @param \BlueSpice\Data\Schema|null $schema An array of meta data items
	 */
	protected function returnData( $resultSet, $schema = null ) {
		\Hooks::run( 'BSApiStoreBaseBeforeReturnData', [
			$this,
			&$resultSet,
			&$schema
		] );
		$apiResult = $this->getResult();

		//Unfortunately \ApiResult does not like \JsonSerializable[], so we
		//need to provide a \stdClass[] or array[]
		$converter = new RecordConverter( $resultSet->getRecords() );
		$records = $converter->convertToRawData();

		$apiResult->setIndexedTagName( $records, $this->root );
		$apiResult->addValue( null, $this->root, $records  );
		$apiResult->addValue( null, $this->totalProperty, $resultSet->getTotal() );
		if ( $schema !== null ) {
			$apiResult->addValue( null, $this->metaData, $schema );
		}
	}

	/**
	 * Called by ApiMain
	 * @return array
	 */
	public function getAllowedParams() {
		return [
			'sort' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '[]',
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-sort',
			],
			'group' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '[]',
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-group',
			],
			'filter' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '[]',
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-filter',
			],
			'page' => [
				static::PARAM_TYPE => 'integer',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => 0,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-page',
			],
			'limit' => [
				static::PARAM_TYPE => 'integer',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => 25,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-limit',
			],
			'start' => [
				static::PARAM_TYPE => 'integer',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => 0,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-start',
			],
			'callback' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-callback',
			],
			'query' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-query',
			],
			'_dc' => [
				static::PARAM_TYPE => 'integer',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-dc',
			],
			'format' => [
				static::PARAM_DFLT => 'json',
				static::PARAM_TYPE => [ 'json', 'jsonfm' ],
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-format',
			],
			'context' => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-store-param-context',
			],
		];
	}

	/**
	 * Using the settings determine the value for the given parameter
	 *
	 * @param string $paramName Parameter name
	 * @param array|mixed $paramSettings Default value or an array of settings
	 *  using PARAM_* constants.
	 * @param bool $parseLimit Whether to parse and validate 'limit' parameters
	 * @return mixed Parameter value
	 */
	protected function getParameterFromSettings( $paramName, $paramSettings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $paramName, $paramSettings, $parseLimit );
		//Unfortunately there is no way to register custom types for parameters
		if ( in_array( $paramName, [ 'sort', 'group', 'filter', 'context' ] ) ) {
			$value = \FormatJson::decode( $value );
			if ( empty( $value ) ) {
				return [];
			}
		}
		return $value;
	}

	/**
	 * Get a value for the given parameter
	 * @param string $paramName Parameter name
	 * @param bool $parseLimit See extractRequestParams()
	 * @return mixed Parameter value
	 */
	public function getParameter( $paramName, $parseLimit = true ) {
		//Make this public, so hook handler could get the params
		return parent::getParameter( $paramName, $parseLimit );
	}

	/**
	 * @return \BlueSpice\Data\IStore
	 */
	protected abstract function makeDataStore();

	/**
	 *
	 * @return ReaderParams
	 */
	protected function getReaderParams() {
		return new ReaderParams( [
			'query' => $this->getParameter( 'query', null ),
			'start' => $this->getParameter( 'start', null ),
			'limit' => $this->getParameter( 'limit', null ),
			'filter' => $this->getParameter( 'filter', null ),
			'sort' => $this->getParameter( 'sort', null ),
		] );
	}

}
