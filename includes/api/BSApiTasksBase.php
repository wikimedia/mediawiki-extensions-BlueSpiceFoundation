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
	 * This is the default log the API writes to. It needs to be registered
	 * in $wgLogTypes
	 * @var string
	 */
	protected $sTaskLogType = null;

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected $aTasks = array();

	/**
	 * Methods that can be executed even when the wiki is in read-mode, as
	 * they do not alter the state/content of the wiki
	 * @var array
	 */
	protected $aReadTasks = array();

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
		$sTask = $aParams['task'];

		$sMethod = 'task_'.$sTask;
		$oResult = $this->makeStandardReturn();

		if( !is_callable( array( $this, $sMethod ) ) ) {
			$oResult->errors['task'] = 'Task '.$sTask.' not implemented';
		}
		else {
			$this->checkTaskPermission( $sTask );
			if( wfReadOnly() && !in_array( $sTask, $this->aReadTasks ) ) {
				global $wgReadOnly;
				$oResult->message = wfMessage( 'bs-readonly', $wgReadOnly )->plain();
			}
			else {
				$oTaskData = $this->getParameter( 'taskData' );
				Hooks::run( 'BSApiTasksBaseBeforeExecuteTask', array( $this, $sTask, &$oTaskData , &$aParams ) );
				$oResult = $this->$sMethod( $oTaskData , $aParams );
				Hooks::run( 'BSApiTasksBaseAfterExecuteTask', array( $this, $sTask, &$oResult, $oTaskData , $aParams ) );
			}
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
	 * Creates a log entry for Special:Log, based on $this->sTaskLogType or
	 * $aOptions['type']. See https://www.mediawiki.org/wiki/Manual:Logging_to_Special:Log
	 * @param string $sAction
	 * @param array $aParams for the log entry
	 * @param array $aOptions <br/>
	 * 'performer' of type User<br/>
	 * 'target' of type Title<br/>
	 * 'timestamp' of type string<br/>
	 * 'relations of type array<br/>
	 * 'deleted' of type int<br/>
	 * 'type' of type string; to allow overriding of class default
	 * @param bool $bDoPublish
	 * @return int Id of the newly created log entry or -1 on error
	 */
	protected function logTaskAction( $sAction, $aParams, $aOptions = array(), $bDoPublish = false ) {
		$aOptions += array(
			'performer' => null,
			'target' => null,
			'timestamp' => null,
			'relations' => null,
			'comment' => null,
			'deleted' =>  null,
			'publish' => null,
			'type' => null //To allow overriding of class default
		);

		$oTarget = $aOptions['target'];
		if ( $oTarget === null ) {
			$oTarget = $this->makeDefaultLogTarget();
		}

		$oPerformer = $aOptions['performer'];
		if ( $oPerformer === null ) {
			$oPerformer = $this->getUser();
		}

		$sType = $this->sTaskLogType;
		if ( $aOptions['type'] !== null ) {
			$sType = $aOptions['type'];
		}

		if ( $sType === null ) { //Not set on class, not set as call option
			return -1;
		}

		$oLogger = new ManualLogEntry( $sType, $sAction );
		$oLogger->setPerformer( $oPerformer );
		$oLogger->setTarget( $oTarget );
		$oLogger->setParameters( $aParams );

		if ( $aOptions['timestamp'] !== null ) {
			$oLogger->setTimestamp( $aOptions['timestamp'] );
		}

		if ( $aOptions['relations'] !== null ) {
			$oLogger->setRelations( $aOptions['relations'] );
		}

		if ( $aOptions['comment'] !== null ) {
			$oLogger->setComment( $aOptions['comment'] );
		}

		if ( $aOptions['deleted'] !== null ) {
			$oLogger->setDeleted( $aOptions['deleted'] );
		}

		$iLogEntryId = $oLogger->insert();

		if ( $bDoPublish ) {
			$oLogger->publish();
		}

		return $iLogEntryId;
	}

	/**
	 *
	 * @return Title
	 */
	protected function makeDefaultLogTarget() {
		return $this->getTitle();
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
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-task-param-task',
			),
			'taskData' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '{}',
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-task-param-taskdata',
			),
			'format' => array(
				ApiBase::PARAM_DFLT => 'json',
				ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-task-param-format',
			),
			'token' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-task-param-token',
			)
		);
	}

	protected function getParameterFromSettings($paramName, $paramSettings, $parseLimit) {
		$value = parent::getParameterFromSettings($paramName, $paramSettings, $parseLimit);
		//Unfortunately there is no way to register custom types for parameters
		if( $paramName === 'taskData' ) {
			$value = FormatJson::decode($value);
			if( empty($value) ) {
				return new stdClass();
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
			'task' => 'The task that should be executed',
			'taskData' => 'JSON string encoded object with arbitrary data for the task',
			'format' => 'The format of the result',
		);
	}

	/**
	 * Returns the basic description for this module
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

	public function checkTaskPermission( $sTask ) {
		$aTaskPermissions = $this->getRequiredTaskPermissions();
		if( empty($aTaskPermissions[$sTask]) ) {
			return;
		}
		foreach( $aTaskPermissions[$sTask] as $sPermission ) {
			if( $this->getUser()->isAllowed( $sPermission ) ) {
				continue;
			}
			//TODO: Reflect permission in error message
			$this->dieUsageMsg( 'badaccess-groups' );
		}
	}

	/**
	 * Returns an array of tasks and their required permissions
	 * array('taskname' => array('read', 'edit'))
	 * @return type
	 */
	protected function getRequiredTaskPermissions() {
		return array();
	}

	/**
	 * General protection
	 * @return string
	 */
	public function needsToken() {
		return 'csrf';
	}
}
