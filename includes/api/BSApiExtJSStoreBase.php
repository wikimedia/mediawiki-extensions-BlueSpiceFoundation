<?php

/**
 *  This class serves as a backend for ExtJS stores. It allows all
 * necessary parameters and provides convenience methods and a standard ouput
 * format
 *
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
abstract class BSApiExtJSStoreBase extends BSApiBase {

	/**
	 * The current parameters sent by the ExtJS store
	 * @var BsExtJSStoreParams
	 */
	protected $oStoreParams = null;

	/**
	 * Automatically set within 'postProcessData' method
	 * @var int
	 */
	protected $iFinalDataSetCount = 0;

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

	public function execute() {
		$aData = $this->makeData();
		$FinalData = $this->postProcessData( $aData );
		$this->returnData( $FinalData );
	}

	protected abstract function makeData();

	/**
	 * Creates a proper output format based on the classes properties
	 * @param array $aData An array of plain old data objects
	 */
	public function returnData($aData) {
		wfRunHooks( 'BSApiExtJSStoreBaseBeforeReturnData', array( $this, &$aData ) );
		$result = $this->getResult();
		$result->setIndexedTagName( $aData, $this->root );
		$result->addValue( null, $this->root, $aData );
		$result->addValue( null, $this->totalProperty, $this->iFinalDataSetCount );
	}

	/**
	 *
	 * @return BsExtJSStoreParams
	 */
	protected function getStoreParams() {
		if( $this->oStoreParams === null ) {
			$this->oStoreParams = BsExtJSStoreParams::newFromRequest();
		}
		return $this->oStoreParams;
	}

	public function getAllowedParams() {
		return array(
			'sort' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '[]'
			),
			'group' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '[]'
			),
			'filter' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '[]'
			),
			'page' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 0
			),
			'limit' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 25
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 0
			),

			'callback' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),

			'query' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'_dc' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' )
			)
		);
	}

	public function getParamDescription() {
		return array(
			'sort' => 'JSON string with sorting info; deserializes to array of objects that hold filed name and direction for each sorting option',
			'group' => 'JSON string with grouping info; deserializes to array of objects that hold filed name and direction for each grouping option',
			'filter' => 'JSON string with filter info; deserializes to array of objects that hold filed name, filter type, and filter value for each sorting option',
			'page' => 'Allows server side calculation of start/limit',
			'limit' => 'Number of results to return',
			'start' => 'The offset to start the result list from',
			'query' => 'This is similar to "filter", but the provided value serves as a filter only for the "value" field of an ExtJS component',
			'callback' => 'The offset to start the result list from',
			'_dc' => '"Disable cache" flag',
			'format' => 'The format of the output (only JSON or formatted JSON)'
		);
	}

	protected function getParameterFromSettings($paramName, $paramSettings, $parseLimit) {
		$value = parent::getParameterFromSettings($paramName, $paramSettings, $parseLimit);
		//Unfortunately there is no way to register custom types for parameters
		if( in_array( $paramName, array( 'sort', 'group', 'filter' ) ) ) {
			$value = FormatJson::decode($value);
			if( empty($value) ) {
				return array();
			}
		}
		return $value;
	}

	/**
	 * Filter, sort and trim the result according to the call parameters and
	 * apply security trimming
	 * @param array $aData
	 * @return array
	 */
	public function postProcessData( $aData ) {
		wfRunHooks( 'BSApiExtJSStoreBaseBeforePostProcessData', array( $this, &$aData ) );
		$aProcessedData = array();

		//First, apply filter
		$aProcessedData = array_filter($aData, array( $this, 'filterCallback') );
		wfRunHooks( 'BSApiExtJSStoreBaseAfterFilterData', array( $this, &$aProcessedData ) );

		//Next, apply sort
		usort($aProcessedData, array( $this, 'sortCallback') );

		//Before we trim, we save the count
		$this->iFinalDataSetCount = count( $aProcessedData );

		//Last, do trimming
		$aProcessedData = $this->trimData( $aProcessedData );

		return $aProcessedData;
	}

	/**
	 * Applies all sorters provided by the store
	 * @param object $oA
	 * @param object $oB
	 * @return int
	 */
	public function sortCallback( $oA, $oB ) {
		$aSort = $this->getParameter('sort');
		$iCount = count( $aSort );
		for( $i = 0; $i < $iCount; $i++ ) {
			$sProperty = $aSort[$i]->property;
			$sDirection = strtoupper( $aSort[$i]->direction );

			if( $oA->$sProperty !== $oB->$sProperty ) {
				if( $sDirection === 'ASC' ) {
					return $oA->$sProperty > $oB->$sProperty ? -1 : 1;
				}
				else { //'DESC'
					return $oA->$sProperty < $oB->$sProperty ? -1 : 1;
				}
			}
		}
		return 0;
	}

	public function filterCallback( $aDataSet ) {
		$aFilter = $this->getParameter('filter');
		foreach( $aFilter as $oFilter ) {
			//If just one of these filters does not apply, the dataset needs
			//to be removed

			if( $oFilter->type == 'string' ) {
				$bFilterApplies = $this->filterString($oFilter, $aDataSet);
				if( !$bFilterApplies ) {
					return false;
				}
			}
			//TODO: Implement for type 'date', 'datetime', 'boolean' and 'numeric'
			//... and even maybe 'list'
		}

		return true;
	}

	public function filterString($oFilter, $aDataSet) {
		$sFieldValue = $aDataSet->{$oFilter->field};
		$sFilterValue = $oFilter->value;

		//TODO: Add string functions to BsStringHelper
		//HINT: http://stackoverflow.com/a/10473026 + Case insensitive
		switch( $oFilter->comparison ) {
			case 'sw':
				return $sFilterValue === '' ||
					strripos($sFieldValue, $sFilterValue, -strlen($sFieldValue)) !== false;
			case 'ew':
				return $sFilterValue === '' ||
					(($temp = strlen($sFieldValue) - strlen($sFilterValue)) >= 0
					&& stripos($sFieldValue, $sFilterValue, $temp) !== false);
			case 'ct':
				return stripos($sFieldValue, $sFilterValue) !== false;
			case 'nct':
				return stripos($sFieldValue, $sFilterValue) === false;
			case 'eq':
				return $sFieldValue === $sFilterValue;
			case 'neq':
				return $sFieldValue !== $sFilterValue;
		}
	}

	public function trimData($aProcessedData) {
		$iStart = $this->getParameter('start');
		$iEnd = $this->getParameter('limit') + $iStart;
		if( $iEnd >= $this->iFinalDataSetCount ) {
			$iEnd = $this->iFinalDataSetCount - 1;
		}

		$aTrimmedData = array();
		for( $i = $iStart; $i <= $iEnd; $i++ ) {
			$aTrimmedData[] = $aProcessedData[$i];
		}

		return $aTrimmedData;
	}
}
