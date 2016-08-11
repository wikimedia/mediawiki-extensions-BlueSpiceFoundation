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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
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
	 * Holds the context of the API call.
	 * @var BSExtendedApiContext
	 */
	protected $oExtendedContext = null;

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
		$this->initContext();

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

				$oResult = $this->validateTaskData( $oTaskData );
				if( empty( $oResult->errors ) && empty( $oResult->message ) ) {
					$oResult = $this->$sMethod( $oTaskData , $aParams );
				}

				Hooks::run( 'BSApiTasksBaseAfterExecuteTask', array( $this, $sTask, &$oResult, $oTaskData , $aParams ) );

				//trigger data update flag after content change over api
				if( $this->isWriteMode() ) {
					$oWikiPage = WikiPage::factory( $this->getTitle() );
					DataUpdate::runUpdates( $oWikiPage->getContent()->getSecondaryDataUpdates( $this->getTitle() ) );
				}
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
			'context' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_DFLT => '{}',
				10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-task-param-context',
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
		if ( in_array( $paramName, array( 'taskData', 'context' ) ) ) {
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
			'context' => 'JSON string encoded object with context data for the task',
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
	 * NOT IMPLEMENTED YET
	 * Return the param definition for each task
	 * array(
	 *    taskname => array(
	 *       paramname => array(
	 *           type => string,
	 *           required => true,
	 *           default => '',
	 *       )
	 *    )
	 * );
	 * @return array - or false to skip validation
	 */
	public function getTaskDataDefinitions() {
		return false;
	}

	/**
	 * NOT IMPLEMENTED YET
	 * Use ParamProcessor to validate taskData params
	 * @param stdClass $oTaskData
	 * @return stdClass - Standard return
	 */
	public function validateTaskData( $oTaskData ) {
		$aDefinitions = $this->getTaskDataDefinitions();
		$oReturn = $this->makeStandardReturn();
		if( $aDefinitions === false ) {
			return $oReturn;
		}
		//TODO: Use ParamProcessor to validate params defined by
		//$this->getTaskDataDefinitions().
		return $oReturn;
	}

	/**
	 * General protection
	 * @return string
	 */
	public function needsToken() {
		return 'csrf';
	}

	/**
	 * Initializes the context of the API call
	 */
	public function initContext() {
		$this->oExtendedContext = BSExtendedApiContext::newFromRequest( $this->getRequest() );
		$this->getContext()->setTitle( $this->oExtendedContext->getTitle() );
		if( $this->getTitle()->getArticleID() > 0 ) {
			//TODO: Check for subtypes like WikiFilePage or WikiCategoryPage
			$this->getContext()->setWikiPage(
				WikiPage::factory( $this->getTitle() )
			);
		}
	}

	/**
	 * MediaWiki initializes all calls to 'api.php' with a Title of 'API'.
	 * By setting the Title object that is provided by our own context
	 * source (the client, e.g. in 'bluespice.api.js/_getContext') we
	 * allow the subclasses of BSApiTaskBase to access '$this->getTitle()'
	 * and retrieve the correct one (e.g "Main_page").
	 * When the context contains a real WikiPage (Article, CategoryPage,
	 * ImagePage, ...), we can also provide the subclass with the correct
	 * object by letting it access '$this->getWikiPage()'.
	 * When there is no valid WikiPage object (e.g. when the context is set
	 * to a SpecialPage) we should make '$this->getWikiPage()' return NULL.
	 * Unfortunately the 'DerivativeContext' used by 'ApiMain' does not
	 * allow this so using '$this->getContext()->setWikiPage( null )' would
	 * crash.
	 * Therefore we just override the relevant methods and do our own checks.
	 *
	 * Returns the current WikiPage object or NULL if not in WikiPage context
	 *
	 * @return WikiPage|null
	 */
	public function getWikiPage() {
		if( $this->getTitle()->getNamespace() < 0 ) {
			return null;
		}
		return parent::getWikiPage();
	}

	/**
	 * @see BSApiTasksBase::getWikiPage
	 */
	public function canUseWikiPage() {
		if( $this->getWikiPage() === null ) {
			return false;
		}
		return parent::canUseWikiPage();
	}

	/*
	 * Indicates whether this module requires write mode
	 * @return bool
	 */
	public function isWriteMode() {
		return true;
	}
}
