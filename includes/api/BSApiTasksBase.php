<?php
/**
 * Provides the base api for BlueSpice.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Patric Wirth <wirth@hallowelt.biz>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Api base class for simple tasks in BlueSpice
 * @package BlueSpice_Foundation
 */
abstract class BSApiTasksBase extends BSApiBase {

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected $aTasks = array();

	/**
	 * The execute() method will be invoked directly by ApiMain immediately
	 * before the result of the module is output. Aside from the
	 * constructor, implementations should assume that no other methods
	 * will be called externally on the module before the result is
	 * processed.
	 * @return null
	 */
	public function execute() {
		$aParams = $this->extractRequestParams();

		//Avoid API warning: register the parameter used to bust browser cache
		$this->getMain()->getVal( '_' );

		$sMethod = 'task_'.$aParams['task'];

		if( !is_callable( array( $this, $sMethod ) ) ) {
			$oResult = $this->makeStandardReturn();
			$oResult->errors['task'] = 'Task '.$aParams['task'].' not implemented';
		}
		else {
			$oResult = $this->$sMethod( $this->getParameter('taskData'), $aParams );
		}

		foreach( $oResult as $sFieldName => $mFieldValue ) {
			if( $mFieldValue === null ) {
				continue; //MW Api doesn't like NULL values
			}
			$this->getResult()->addValue(null, $sFieldName, $mFieldValue);
		}
	}

	/**
	 * Standard return object
	 * Every task should return this!
	 * @return BSStandardAPIResponse
	 */
	protected function makeStandardReturn() {
		return new BSStandardAPIResponse();
	}

	/**
	 * Returns an array of allowed parameters
	 * @return array
	 */
	protected function getAllowedParams() {
		return array(
			'task' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => $this->aTasks,
			),
			'taskData' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '{}'
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
			)
		);
	}

	protected function getParameterFromSettings($paramName, $paramSettings, $parseLimit) {
		$value = parent::getParameterFromSettings($paramName, $paramSettings, $parseLimit);
		//Unfortunately there is no way to register custom types for parameters
		if( $paramName === 'taskData' ) {
			$value = FormatJson::decode($value);
			if( empty($value) ) {
				return array();
			}
		}
		return $value;
	}

	/**
	 * Returns the basic param descriptions
	 * @return array
	 */
	public function getParamDescription() {
		return array(
			'task' => 'The task you would like to execute',
			'taskData' => 'JSON string encoded object with arbitrary data for the task',
			'format' => 'The format of the result',
		);
	}

	/**
	 * Returns the bsic description for this module
	 * @return type
	 */
	public function getDescription() {
		return array(
			'BSApiTasksBase: This should be implemented by subclass'
		);
	}

	/**
	 * Returns the basic example
	 * @return type
	 */
	public function getExamples() {
		return array(
			'api.php?action='.$this->getModuleName().'&task='.$this->aTasks[0].'&taskData={someKey:"someValue",isFalse:true}',
		);
	}
}