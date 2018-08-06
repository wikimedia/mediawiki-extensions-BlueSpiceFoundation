<?php

namespace BlueSpice;

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
abstract class StoreApiBase extends \BSApiBase {

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
		$this->initContext();
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
		\Hooks::run( 'BSApiStoreBaseBeforeReturnData', array( $this, &$resultSet, &$schema ) );
		$apiResult = $this->getResult();

		//Unfortunately \ApiResult does not like \JsonSerializable[], so we
		//need to provide a \stdClass[] or array[]
		$converter = new Data\RecordConverter( $resultSet->getRecords() );
		$records = $converter->convertToRawData();

		$apiResult->setIndexedTagName( $records, $this->root );
		$apiResult->addValue( null, $this->root, $records  );
		$apiResult->addValue( null, $this->totalProperty, $resultSet->getTotal() );
		if( $schema !== null ) {
			$apiResult->addValue( null, $this->metaData, $schema );
		}
	}

	/**
	 * Called by ApiMain
	 * @return array
	 */
	public function getAllowedParams() {
		return array(
			'sort' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => '[]',
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-sort',
			),
			'group' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => '[]',
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-group',
			),
			'filter' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => '[]',
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-filter',
			),
			'page' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => 0,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-page',
			),
			'limit' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => 25,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-limit',
			),
			'start' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => 0,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-start',
			),
			'callback' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-callback',
			),
			'query' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-query',
			),
			'_dc' => array(
				\ApiBase::PARAM_TYPE => 'integer',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-dc',
			),
			'format' => array(
				\ApiBase::PARAM_DFLT => 'json',
				\ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-format',
			),
			'context' => array(
				\ApiBase::PARAM_TYPE => 'string',
				\ApiBase::PARAM_REQUIRED => false,
				\ApiBase::PARAM_DFLT => '{}',
				\ApiBase::PARAM_HELP_MSG => 'apihelp-bs-store-param-context',
			),
		);
	}

	public function getParamDescription() {
		return array(
			'sort' => 'JSON string with sorting info; deserializes to "array of objects" that hold field name and direction for each sorting option',
			'group' => 'JSON string with grouping info; deserializes to "array of objects" that hold field name and direction for each grouping option',
			'filter' => 'JSON string with filter info; deserializes to "array of objects" that hold field name, filter type, and filter value for each filtering option',
			'page' => 'Allows server side calculation of start/limit',
			'limit' => 'Number of results to return',
			'start' => 'The offset to start the result list from',
			'query' => 'Similar to "filter", but the provided value serves as a filter only for the "value" field of an ExtJS component',
			'callback' => 'A method name in the client code that should be called in the response (JSONP)',
			'_dc' => '"Disable cache" flag',
			'format' => 'The format of the output (only JSON or formatted JSON)',
			'context' => 'JSON string encoded object with context data for the store',
		);
	}

	protected function getParameterFromSettings( $paramName, $paramSettings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $paramName, $paramSettings, $parseLimit );
		//Unfortunately there is no way to register custom types for parameters
		if( in_array( $paramName, [ 'sort', 'group', 'filter', 'context' ] ) ) {
			$value = \FormatJson::decode( $value );
			if( empty( $value ) ) {
				return [];
			}
		}
		return $value;
	}

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
	 * @return \BlueSpice\Data\ReaderParams
	 */
	protected function getReaderParams() {
		return new \BlueSpice\Data\ReaderParams([
			'query' => $this->getParameter( 'query', null ),
			'start' => $this->getParameter( 'start', null ),
			'limit' => $this->getParameter( 'limit', null ),
			'filter' => $this->getParameter( 'filter', null ),
			'sort' => $this->getParameter( 'sort', null ),
		]);
	}

	/**
	 * Initializes the context of the API call
	 */
	protected function initContext() {
		$this->extendedContext = \BSExtendedApiContext::newFromRequest(
			$this->getRequest()
		);
		$this->getContext()->setTitle( $this->extendedContext->getTitle() );
		if( $this->getTitle()->getArticleID() > 0 ) {
			//TODO: Check for subtypes like WikiFilePage or WikiCategoryPage
			$this->getContext()->setWikiPage(
				\WikiPage::factory( $this->getTitle() )
			);
		}
	}

}
