<?php
/**
 *  This class serves as a backend for ExtJS stores. It allows all
 * necessary parameters and provides convenience methods and a standard ouput
 * format
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * l1 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
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

use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Services;

abstract class BSApiExtJSStoreBase extends BSApiBase {

	const SECONDARY_FIELD_PLACEHOLDER = '<added as secondary field>';
	const PROP_SPEC_FILTERABLE = 'filterable';
	const PROP_SPEC_SORTABLE = 'sortable';

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

	/**
	 * May be overwritten by subclass
	 * @var string
	 */
	protected $metaData = 'metadata';

	/**
	 *
	 * @var LinkRenderer
	 */
	protected $oLinkRenderer = null;

	public function execute() {
		$this->oLinkRenderer = Services::getInstance()->getLinkRenderer();

		$sQuery = $this->getParameter( 'query' );
		$aData = $this->makeData( $sQuery );
		$aMetaData = $this->makeMetaData();
		$aFinalData = $this->postProcessData( $aData );
		$this->returnData( $aFinalData, $aMetaData );
	}

	/**
	 * @param string $sQuery Potential query provided by ExtJS component.
	 * This is some kind of pre-filtering. Subclass has to decide if
	 * and how to process it
	 * @return array - Full list of of data objects. Filters, paging, sorting
	 * will be done by the base class
	 */
	protected abstract function makeData( $sQuery = '' );

	/**
	 * @return array - a meta data specification in form
	 *  [ 'properties' => [ <property_name> => <spec>, ... ], ... ]
	 * where <spec> is an array compatible to
	 * https://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.column.Column
	 */
	protected function makeMetaData() {
		return array();
	}

	/**
	 * Creates a proper output format based on the classes properties
	 * @param array $aData An array of plain old data objects
	 * @param array $aMetaData An array of meta data items
	 */
	public function returnData( $aData, $aMetaData = array() ) {
		Hooks::run( 'BSApiExtJSStoreBaseBeforeReturnData', array( $this, &$aData, &$aMetaData ) );
		$result = $this->getResult();
		$result->setIndexedTagName( $aData, $this->root );
		$result->addValue( null, $this->root, $aData );
		$result->addValue( null, $this->totalProperty, $this->iFinalDataSetCount );
		if( !empty( $aMetaData ) ) {
			$result->addValue( null, $this->metaData, $aMetaData );
		}
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
				ApiBase::PARAM_DFLT => '[]',
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-sort',
			),
			'group' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '[]',
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-group',
			),
			'filter' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '[]',
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-filter',
			),
			'page' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 0,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-page',
			),
			'limit' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 25,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-limit',
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => 0,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-start',
			),

			'callback' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-callback',
			),

			'query' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-query',
			),
			'_dc' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => false,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-dc',
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-format',
			),
			'context' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-context',
			]
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
			'context' => 'Context in which the call is made'
		);
	}

	protected function getParameterFromSettings($paramName, $paramSettings, $parseLimit) {
		$value = parent::getParameterFromSettings($paramName, $paramSettings, $parseLimit);
		//Unfortunately there is no way to register custom types for parameters
		if( in_array( $paramName, array( 'sort', 'group', 'filter', 'context' ) ) ) {
			$value = FormatJson::decode( $value );
			if( empty($value) ) {
				return array();
			}
		}
		return $value;
	}

	public function getParameter( $paramName, $parseLimit = true ) {
		//Make this public, so hook handler could get the params
		return parent::getParameter( $paramName, $parseLimit );
	}

	/**
	 * Filter, sort and trim the result according to the call parameters and
	 * apply security trimming
	 * @param array $aData
	 * @return array
	 */
	public function postProcessData( $aData ) {
		if( !Hooks::run( 'BSApiExtJSStoreBaseBeforePostProcessData', array( $this, &$aData ) ) ) {
			return $aData;
		}

		$aProcessedData = array();

		//First, apply filter
		$aProcessedData = array_filter($aData, array( $this, 'filterCallback') );
		Hooks::run( 'BSApiExtJSStoreBaseAfterFilterData', array( $this, &$aProcessedData ) );

		//Next, apply sort
		//usort($aProcessedData, array( $this, 'sortCallback') ); <-- had some performance issues
		$aProcessedData = $this->sortData( $aProcessedData );

		//Before we trim, we save the count
		$this->iFinalDataSetCount = count( $aProcessedData );

		//Last, do trimming
		$aProcessedData = $this->trimData( $aProcessedData );

		//Add secondary fields
		$aProcessedData = $this->addSecondaryFields( $aProcessedData );

		return $aProcessedData;
	}

	/**
	 * Applies all sorters provided by the store
	 * --> has performance issues on large dataset; Code kept for documentation
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
					return $oA->$sProperty < $oB->$sProperty ? -1 : 1;
				}
				else { //'DESC'
					return $oA->$sProperty > $oB->$sProperty ? -1 : 1;
				}
			}
		}
		return 0;
	}

	/**
	 *
	 * @param object $aDataSet
	 * @return boolean
	 */
	public function filterCallback( $aDataSet ) {
		$aFilter = $this->getParameter( 'filter' );
		$aUnfilterableProps = $this->getPropertyNamesBySpecValue(
			self::PROP_SPEC_FILTERABLE, false
		);

		foreach( $aFilter as $oFilter ) {
			//If just one of these filters does not apply, the dataset needs
			//to be removed

			if( empty( $oFilter->type ) ) {
				continue;
			}

			if( !isset( $oFilter->field ) && isset( $oFilter->property ) ) {
				$oFilter->field = $oFilter->property;
			}

			if( !isset( $oFilter->comparison ) && isset( $oFilter->operator ) ) {
				$oFilter->comparison = $oFilter->operator;
			}

			if( in_array( $oFilter->field, $aUnfilterableProps ) ) {
				continue;
			}

			if( $oFilter->type == 'string' ) {
				$bFilterApplies = $this->filterString( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}
			if( $oFilter->type == 'list' ) {
				$bFilterApplies = $this->filterList( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}
			if( $oFilter->type == 'numeric' ) {
				$bFilterApplies = $this->filterNumeric( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}
			if( $oFilter->type == 'boolean' ) {
				$bFilterApplies = $this->filterBoolean( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}

			if( $oFilter->type == 'date' ) {
				$bFilterApplies = $this->filterDate( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}
			//TODO: Implement for type 'datetime'

			if( $oFilter->type == 'title' ) {
				$bFilterApplies = $this->filterTitle( $oFilter, $aDataSet );
				if( !$bFilterApplies ) {
					return false;
				}
			}

			if( $oFilter->type == 'templatetitle' ) {
				$bFilterApplies = $this->filterTitle( $oFilter, $aDataSet, NS_TEMPLATE );
				if( !$bFilterApplies ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Performs string filtering based on given filter of type string on a dataset
	 * @param object $oFilter
	 * @param oject $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterString( $oFilter, $aDataSet ) {
		if( !is_string( $oFilter->value ) ) {
			return true; //TODO: Warning
		}
		if ( !isset( $aDataSet->{$oFilter->field} ) ) {
			return false;
		}
		$sFieldValue = $aDataSet->{$oFilter->field};
		$sFilterValue = $oFilter->value;

		return BsStringHelper::filter( $oFilter->comparison, $sFieldValue, $sFilterValue );
	}

	/**
	 * Performs numeric filtering based on given filter of type integer on a
	 * dataset
	 * @param object $oFilter
	 * @param oject $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterNumeric( $oFilter, $aDataSet ) {
		if( !is_numeric( $oFilter->value ) ) {
			return true; //TODO: Warning
		}
		$iFieldValue = (int) $aDataSet->{$oFilter->field};
		$iFilterValue = (int) $oFilter->value;

		switch( $oFilter->comparison ) {
			case 'gt':
				return $iFieldValue > $iFilterValue;
			case 'lt':
				return $iFieldValue < $iFilterValue;
			case 'eq':
				return $iFieldValue === $iFilterValue;
			case 'neq':
				return $iFieldValue !== $iFilterValue;
		}
	}

	/**
	 * Performs list filtering based on given filter of type array on a dataset
	 * @param object $oFilter
	 * @param oject $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterList( $oFilter, $aDataSet ) {
		if( !is_array( $oFilter->value ) ) {
			return true; //TODO: Warning
		}
		$aFieldValues = $aDataSet->{$oFilter->field};
		if( empty( $aFieldValues ) ) {
			return false;
		}
		$aFilterValues = $oFilter->value;
		$aTemp = array_intersect( $aFieldValues, $aFilterValues );
		if( empty( $aTemp ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Performs filtering based on given filter of type bool on a dataset
	 * @param object $oFilter
	 * @param object $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterBoolean( $oFilter, $aDataSet ) {
		return $oFilter->value == $aDataSet->{ $oFilter->field };
	}

	/**
	 * Performs filtering based on given filter of type date on a dataset
	 * "Ext.ux.grid.filter.DateFilter" by default sends filter value in format
	 * of m/d/Y
	 * @param object $oFilter
	 * @param object $aDataSet
	 */
	public function filterDate( $oFilter, $aDataSet ) {
		$iFilterValue = strtotime( $oFilter->value ); // Format: "m/d/Y"
		$iFieldValue = strtotime( $aDataSet->{$oFilter->field} ); // Format "YmdHis", or something else...

		switch( $oFilter->comparison ) {
			case 'gt':
				return $iFieldValue > $iFilterValue;
			case 'lt':
				return $iFieldValue < $iFilterValue;
			case 'eq':
				//We need to normalise the date on day-level
				$iFieldValue = strtotime(
					date( 'm/d/Y', $iFieldValue )
				);
				return $iFieldValue === $iFilterValue;
		}
	}

	/**
	 * Performs string filtering based on given filter of type title on a dataset
	 * @param object $oFilter
	 * @param oject $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterTitle( $oFilter, $aDataSet, $iDefaultNs = NS_MAIN ) {
		if( !is_string( $oFilter->value ) ) {
			return true; //TODO: Warning
		}
		$oFieldValue = Title::newFromText( $aDataSet->{$oFilter->field}, $iDefaultNs );
		$oFilterValue = Title::newFromText( $oFilter->value, $iDefaultNs  );

		switch( $oFilter->comparison ) {
			case 'gt':
				return Title::compare( $oFieldValue, $oFilterValue ) > 0;
			case 'lt':
				return Title::compare( $oFieldValue, $oFilterValue ) < 0;
			case 'eq':
				return Title::compare( $oFieldValue, $oFilterValue ) == 0;
			case 'neq':
				return Title::compare( $oFieldValue, $oFilterValue ) != 0;
		}
	}

	/**
	 * Applies pagination on the result
	 * @param array $aProcessedData The filtered result
	 * @return array a trimmed version of the result
	 */
	public function trimData( $aProcessedData ) {
		$iStart = $this->getParameter( 'start' );
		$iEnd = $this->getParameter( 'limit' ) + $iStart;

		if( $iEnd > $this->iFinalDataSetCount || $iEnd === 0 ) {
			$iEnd = $this->iFinalDataSetCount;
		}

		$aTrimmedData = array();
		for( $i = $iStart; $i < $iEnd; $i++ ) {
			$aTrimmedData[] = $aProcessedData[$i];
		}

		return $aTrimmedData;
	}

	/**
	 * Performs fast sorting on the results. Thanks to user "pigpen"
	 * @param array $aProcessedData
	 * @return array The sorted results
	 */
	public function sortData($aProcessedData) {
		$aSort = $this->getParameter('sort');
		$iCount = count( $aSort );
		$aUnsortableProps = $this->getPropertyNamesBySpecValue(
			self::PROP_SPEC_SORTABLE, false
		);
		$aParams = array();
		for( $i = 0; $i < $iCount; $i++ ) {
			$sProperty = $aSort[$i]->property;
			if( in_array( $sProperty, $aUnsortableProps ) ) {
				continue;
			}
			$sDirection = strtoupper( $aSort[$i]->direction );
			$a{$sProperty} = array();
			foreach( $aProcessedData as $iKey => $oDataSet ) {
				$a{$sProperty}[$iKey] = $this->getSortValue( $oDataSet, $sProperty );
			}

			$aParams[] = $a{$sProperty};
			if( $sDirection === 'ASC' ) {
				$aParams[] = SORT_ASC;
			}
			else {
				$aParams[] = SORT_DESC;
			}
			$aParams[] = $this->getSortFlags( $sProperty );
		}

		if( !empty( $aParams ) ) {
			$aParams[] = &$aProcessedData;
			call_user_func_array( 'array_multisort', $aParams );
		}

		return array_values( $aProcessedData );
	}

	/**
	 * Returns the flags for PHP 'array_multisort' function
	 * May be overridden by subclasses to provide different sort flags
	 * depending on the property
	 * @param string $sProperty
	 * @return int see http://php.net/manual/en/array.constants.php for details
	 */
	protected function getSortFlags( $sProperty ) {
		return SORT_NATURAL;
	}

	/**
	 * Returns the value a for a field a dataset is being sorted by.
	 * May be overridden by subclass to allow custom sorting
	 * @param stdClass $oDataSet
	 * @param string $sProperty
	 * @return string
	 */
	protected function getSortValue( $oDataSet, $sProperty ) {
		$mValue = $oDataSet->{$sProperty};
		if( is_array( $mValue ) ) {
			return $this->getSortValueFromList( $mValue, $oDataSet, $sProperty );
		}

		return $mValue;
	}

	/**
	 * Normalizes an array to a string value that can be used in sort logic.
	 * May be overridden by subclass to customize sorting.
	 * Assumes that array entries can be casted to string.
	 * @param array $aValues
	 * @param stdClass $oDataSet
	 * @param string $sProperty
	 * @return string
	 */
	protected function getSortValueFromList( $aValues, $oDataSet, $sProperty ) {
		$sCombinedValue = '';
		foreach( $aValues as $sValue ) {
			// PHP 7 workaround. In PHP 7 cast throws no exception. It's a fatal error so i can't catch it :-(
			if( $this->canBeCastedToString( $sValue ) )
			{
				$sCombinedValue .= (string)$sValue;
			} else {
				$sCombinedValue .= FormatJson::encode( $sValue );
			}
		}
		return $sCombinedValue;
	}

	/**
	 * May be overridden by subclass to add additional fields to the data sets
	 * ATTENTION: Those fields are not filterable and sortable! This should be
	 * declared in "makeMetadata"
	 * @param array $aTrimmedData
	 * @return array
	 */
	protected function addSecondaryFields( $aTrimmedData ) {
		return $aTrimmedData;
	}

	/**
	 * Searches properties defined in the metadata for a certain specification
	 * value
	 * @param string $sSpecName
	 * @param mixed $mSpecValue
	 * @return array Property names
	 */
	protected function getPropertyNamesBySpecValue( $sSpecName, $mSpecValue ) {
		$aMeta = $this->makeMetaData();
		$aFoundPropNames = [];
		if( !isset( $aMeta['properties'] ) ) {
			return $aFoundPropNames;
		}

		foreach( $aMeta['properties'] as $sPropName => $aPropSpec ) {
			if( isset( $aPropSpec[$sSpecName] ) &&  $aPropSpec[$sSpecName] === $mSpecValue ) {
				$aFoundPropNames[] = $sPropName;
			}
		}

		return $aFoundPropNames;
	}

	/**
	 * Checks if a array or object ist castable to string.
	 *
	 * @param mixed $mValue
	 * @return bool
	 */
	private function canBeCastedToString( $mValue ) {
		if ( !is_array( $mValue ) &&
			( !is_object( $mValue ) && settype( $mValue, 'string' ) !== false ) ||
			( is_object( $mValue ) && method_exists( $mValue, '__toString' ) ) ) {
			return true;
		} else {
			return false;
		}
	}
}
