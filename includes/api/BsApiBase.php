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
 * @version    2.23.0
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Api base class for BlueSpice
 * @package BlueSpice_Foundation
 */
abstract class BsApiBase extends ApiBase {

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected static $aTasks = array();

	/**
	 * Constructor
	 * @param $mainModule ApiMain object
	 * @param string $moduleName Name of this module
	 * @param string $modulePrefix Prefix to use for parameter names
	 */
	public function __construct( $query, $moduleName, $modulePrefix = '' ) {
		parent::__construct( $query, $moduleName, $modulePrefix );
	}

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

		if( !isset($aParams['task']) ) return;

		if( in_array($aParams['task'], static::$aTasks) ) {
			$oResult = call_user_func(
				array($this, $aParams['task']),
				$aParams
			);
		}

		$this->getResult()->addValue(null, 'bs', $oResult);
	}

	/**
	 * Standard return object
	 * Every task should return this!
	 * @return object
	 */
	protected static function stdReturn() {
		return $oReturn = (object) array(
			'result' => array(
				'payload' => null,
				'success' => false,
				'message' => '',
				'errors' => array(),
				'payload_count' => 0,
			)
		);
	}

	/**
	 * Returns an array of allowed parameters
	 * @return array
	 */
	protected function getAllowedParams() {
		return array(
			'task' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'string'
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
			)
		);
	}

	/**
	 * Returns the basic param descriptions
	 * @return array
	 */
	public function getParamDescription() {
		return array(
			'task' => 'The task you would like to execute',
			'format' => 'The format of the result',
		);
	}

	/**
	 * Default to false
	 * @return boolean
	 */
	public function needsToken() {
		return false;
	}

	/**
	 * Default to empty string
	 * @return string
	 */
	public function getTokenSalt() {
		return '';
	}

	/**
	 * Default to true
	 * @return boolean
	 */
	public function mustBePosted() {
		return true;
	}

	/**
	 * Default to false
	 * @return boolean
	 */
	public function isWriteMode() {
		return false;
	}

	/**
	 * Returns the bsic description for this module
	 * @return type
	 */
	public function getDescription() {
		return array(
			'BsApiBase: This should be implemented by subclass'
		);
	}

	/**
	 * Returns the basic example
	 * @return type
	 */
	public function getExamples() {
		return array(
			'api.php?action=<childapimodule>&task=<taskofchildapimodule>',
		);
	}
}