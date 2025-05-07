<?php
/**
 * This class serves as a backend for the generic database table store.
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
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 *
 * Example request parameters of an ExtJS store
 */
abstract class BSApiExtJSDBTableStoreBase extends BSApiExtJSStoreBase {

	/** @var array */
	protected $aTables = [];
	/** @var array */
	protected $aFields = [];
	/** @var array */
	protected $aConditions = [];
	/** @var array */
	protected $aOptions = [];
	/** @var array */
	protected $aJoinOptions = [];

	/**
	 * @param string $sQuery Potential query provided by ExtJS component.
	 * This is some kind of preliminary filtering. Subclass has to decide if
	 * and how to process it
	 * @return array - Full list of of data objects. Filters, paging, sorting
	 * will be done by the base class
	 */
	protected function makeData( $sQuery = '' ) {
		$aData = [];

		$aFilter = $this->getParameter( 'filter' );

		$this->aTables = $this->makeTables( $sQuery, $aFilter );
		$this->aFields = $this->makeFields( $sQuery, $aFilter );
		$this->aConditions = $this->makeConditions( $sQuery, $aFilter );
		$this->aOptions  = $this->makeOptions( $sQuery, $aFilter );
		$this->aJoinOptions = $this->makeJoinOptions( $sQuery, $aFilter );

		$this->services->getHookContainer()->run( 'BSApiExtJSDBTableStoreBeforeQuery', [
			$this,
			$sQuery,
			$aFilter,
			&$this->aTables,
			&$this->aFields,
			&$this->aConditions,
			&$this->aOptions,
			&$this->aJoinOptions,
			&$aData,
		] );

		$res = $this->getDB()->select(
			$this->aTables,
			$this->aFields,
			$this->aConditions,
			__METHOD__,
			$this->aOptions,
			$this->aJoinOptions
		);

		// TODO error
		if ( !$res ) {
			return $aData;
		}

		foreach ( $res as $row ) {
			$oData = $this->makeDataSet( $row );
			if ( !$oData ) {
				continue;
			}
			$aData[] = $oData;
		}

		$this->services->getHookContainer()->run( 'BSApiExtJSDBTableStoreAfterQuery', [
			$this,
			$sQuery,
			$aFilter,
			$this->aTables,
			$this->aFields,
			$this->aConditions,
			$this->aOptions,
			$this->aJoinOptions,
			&$aData,
		] );

		return $aData;
	}

	/**
	 * @param string $sQuery
	 * @param array $aFilter
	 * @return array
	 */
	abstract public function makeTables( $sQuery, $aFilter );

	/**
	 *
	 * @param string $sQuery
	 * @param array $aFilter
	 * @return array
	 */
	public function makeFields( $sQuery, $aFilter ) {
		return [
			'*'
		];
	}

	/**
	 *
	 * @param string $sQuery
	 * @param array $aFilter
	 * @return array
	 */
	public function makeConditions( $sQuery, $aFilter ) {
		$aReturn = [];
		if ( empty( $aFilter ) ) {
			return $aReturn;
		}

		foreach ( $aFilter as $oFilter ) {
			if ( !in_array( $oFilter->field, $this->aFields ) ) {
				continue;
			}
			if ( $oFilter->type == 'numeric' ) {
				$aReturn = $this->buildConditionNumeric( $oFilter, $aReturn );
			}
			if ( $oFilter->type == 'string' ) {
				$aReturn = $this->buildConditionString( $oFilter, $aReturn );
			}
		}
		return $aReturn;
	}

	/**
	 *
	 * @param string $sQuery
	 * @param array $aFilter
	 * @return array
	 */
	public function makeOptions( $sQuery, $aFilter ) {
		return [

		];
	}

	/**
	 *
	 * @param string $sQuery
	 * @param array $aFilter
	 * @return array
	 */
	public function makeJoinOptions( $sQuery, $aFilter ) {
		return [

		];
	}

	/**
	 * Returns a single data set
	 * @param type $row
	 * @return stdClass or null
	 */
	public function makeDataSet( $row ) {
		return (object)$row;
	}

	/**
	 *
	 * @param \stdClass $oFilter
	 * @param array $aReturn
	 * @return array
	 */
	public function buildConditionNumeric( $oFilter, $aReturn = [] ) {
		if ( !is_numeric( $oFilter->value ) ) {
			// TODO: Warning
			return $aReturn;
		}
		$iFilterValue = (int)$oFilter->value;

		switch ( $oFilter->comparison ) {
			case 'gt':
				$aReturn[] = "$oFilter->field > $iFilterValue";
				break;
			case 'lt':
				$aReturn[] = "$oFilter->field < $iFilterValue";
				break;
			case 'eq':
				$aReturn[$oFilter->field] = $iFilterValue;
				break;
			case 'neq':
				$aReturn[] = "$oFilter->field != $iFilterValue";
		}

		return $aReturn;
	}

	/**
	 *
	 * @param \stdClass $oFilter
	 * @param array $aReturn
	 * @return array
	 */
	public function buildConditionString( $oFilter, $aReturn = [] ) {
		if ( !is_string( $oFilter->value ) ) {
			// TODO: Warning
			return $aReturn;
		}
		$sFilterValue = (string)$oFilter->value;

		$oDB = $this->getDB();
		switch ( $oFilter->comparison ) {
			case 'ew':
				$aReturn[] = $oFilter->field . " " . $oDB->buildLike(
					$oDB->anyString(),
					$sFilterValue
				);
				break;
			case 'sw':
				$aReturn[] = $oFilter->field . " " . $oDB->buildLike(
					$sFilterValue,
					$oDB->anyString()
				);
				break;
			case 'eq':
				$aReturn[$oFilter->field] = $sFilterValue;
				break;
			case 'neq':
				$aReturn[] = "$oFilter->field != $sFilterValue";
				break;
			case 'ct':
				$aReturn[] = $oFilter->field . " " . $oDB->buildLike(
					$oDB->anyString(),
					$sFilterValue,
					$oDB->anyString()
				);
				break;
			case 'nct':
				$sAny = $oDB->anyString()->toString();
				$aReturn[]
					= "$oFilter->field NOT LIKE '$sAny{$sFilterValue}$sAny'";
		}

		return $aReturn;
	}

}
