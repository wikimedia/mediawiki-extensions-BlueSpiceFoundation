<?php
/**
 * DEPRECATED!
 * Provides the base task api for BlueSpice.
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
 * @deprecated since version 3.1 - Implement \BluseSpice\ITask and use
 * the TaskRegistry in extension.json to be able to call your task in the new
 * generic task api BlueSpice\Api\Task with 'bs-task'
 * @author     Patric Wirth
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

use BlueSpice\Api\Response\Standard;
use BlueSpice\UtilityFactory;
use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiMain;
use MediaWiki\Api\ApiUsageException;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\Rdbms\DBError;

/**
 * Api base class for simple tasks in BlueSpice
 * @package BlueSpice_Foundation
 */
abstract class BSApiTasksBase extends \BlueSpice\Api {

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $mPrimaryDB = null;

	/**
	 * This is the default log the API writes to. It needs to be registered
	 * in $wgLogTypes
	 * @var string
	 */
	protected $sTaskLogType = null;

	/**
	 * Methods that can be called by task param
	 * e.g.
	 * [
	 *    'taskname' => [
	 *       'examples' => [
	 *           [
	 *               'paramname' => 'Some string'
	 *           ]
	 *       ],
	 *       'params' => [
	 *           'paramname' => [
	 *               'type' => string,
	 *               'required' => true
	 *           ]
	 *        ]
	 *     ]
	 * ];
	 * @var array
	 */
	protected $aTasks = [];

	/**
	 * Global available bs api tasks, can be called by task param, extends $aTasks
	 * @var array
	 */
	protected $aGlobalTasks = [ 'getUserTaskPermissions' ];

	/**
	 * Methods that can be executed even when the wiki is in read-mode, as
	 * they do not alter the state/content of the wiki
	 * @var array
	 */
	protected $aReadTasks = [];

	/**
	 *
	 * @var BSTasksApiSpec
	 */
	protected $oTasksSpec = null;

	/**
	 *
	 * @var UtilityFactory
	 */
	protected $utilityFactory = null;

	/**
	 * DEPRECATED!
	 * @param ApiMain $mainModule
	 * @param string $moduleName Name of this module
	 * @param string $modulePrefix Prefix to use for parameter names
	 * @deprecated since version 3.1 - Implement \BluseSpice\ITask and use
	 * the TaskRegistry in extension.json to be able to call your task in the new
	 * generic task api BlueSpice\Api\Task with 'bs-task'
	 */
	public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		parent::__construct( $mainModule, $moduleName, $modulePrefix );
		$this->aTasks = array_merge( $this->aTasks, $this->aGlobalTasks );
		$this->oTasksSpec = new BSTasksApiSpec( $this->aTasks );
		$this->utilityFactory = $this->services->getService( 'BSUtilityFactory' );
	}

	/**
	 * DEPRECATED!
	 * The execute() method will be invoked directly by ApiMain immediately
	 * before the result of the module is output. Aside from the
	 * constructor, implementations should assume that no other methods
	 * will be called externally on the module before the result is
	 * processed.
	 * @deprecated since version 3.1 - Implement \BluseSpice\ITask and use
	 * the TaskRegistry in extension.json to be able to call your task in the new
	 * generic task api BlueSpice\Api\Task with 'bs-task'
	 * @return null
	 */
	public function execute() {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$aParams = $this->extractRequestParams();

		/**
		 * As we disable "needToken" of one of the following flags is set we
		 * need to make sure that no task is being executed!
		 */
		if ( isset( $aParams['schema'] ) ) {
			return $this->returnTaskDataSchema( $aParams['task'] );
		}
		if ( isset( $aParams['examples'] ) ) {
			return $this->returnTaskDataExamples( $aParams['task'] );
		}

		// Avoid API warning: register the parameter used to bust browser cache
		$this->getMain()->getVal( '_' );
		$sTask = $aParams['task'];

		$sMethod = 'task_' . $sTask;
		$oResult = $this->makeStandardReturn();

		if ( !is_callable( [ $this, $sMethod ] ) ) {
			$oResult->errors['task'] = "Task '$sTask' not implemented!";
		} else {
			$res = $this->checkTaskPermission( $sTask );
			if ( !$res ) {
				$this->dieWithPermissionError();
			}
			if ( MediaWikiServices::getInstance()->getReadOnlyMode()->isReadOnly()
				&& !in_array( $sTask, $this->aReadTasks )
			) {
				$oResult->message = wfMessage( 'bs-readonly',
					MediaWikiServices::getInstance()->getReadOnlyMode()->getReason() )->text();
			} else {
				$oTaskData = $this->getParameter( 'taskData' );
				$this->services->getHookContainer()->run(
					'BSApiTasksBaseBeforeExecuteTask',
					[
						$this,
						$sTask,
						&$oTaskData,
						&$aParams
					]
				);
				$this->checkTaskPermissionsAgainstTaskDataTitles( $sTask, $oTaskData );

				$oResult = $this->validateTaskData( $sTask, $oTaskData );
				if ( empty( $oResult->errors ) && empty( $oResult->message ) ) {
					try {
						$oResult = $this->$sMethod( $oTaskData, $aParams );
					} catch ( Exception $e ) {
						$oResult->success = false;
						$oResult->message = $e->getMessage();
						$mCode = method_exists( $e, 'getCodeString' ) ? $e->getCodeString() : $e->getCode();
						if ( $e instanceof DBError ) {
							// TODO: error code for subtypes like DBQueryError or DBReadOnlyError?
							$mCode = 'dberror';
						}
						if ( $mCode === 0 ) {
							$mCode = 'error-0';
						}
						$oResult->errors[$mCode] = $e->getMessage();
						$oResult->errors[0]['code'] = 'unknown error';
					}
				}

				$this->services->getHookContainer()->run( 'BSApiTasksBaseAfterExecuteTask', [
					$this,
					$sTask,
					&$oResult,
					$oTaskData,
					$aParams
				] );
			}
		}

		foreach ( $oResult as $sFieldName => $mFieldValue ) {
			if ( $mFieldValue === null ) {
				// MW Api doesn't like NULL values
				continue;
			}

			// Remove empty 'errors' array from respons as mw.Api in MW 1.30+
			// will interpret this field as indicator for a failed request
			if ( $sFieldName === 'errors' && empty( $mFieldValue ) ) {
				continue;
			}
			$this->getResult()->addValue( null, $sFieldName, $mFieldValue );
		}
	}

	/**
	 * trigger data update flag after content change over api
	 * @param Title|null $oTitle
	 */
	protected function runUpdates( $oTitle = null ) {
		if ( $oTitle === null ) {
			$oTitle = $this->getTitle();
		}
		if ( !$oTitle ) {
			return;
		}
		if ( !$this->isWriteMode() ) {
			return;
		}
		$dataUpdater = $this->services->getService( 'BSSecondaryDataUpdater' );
		$dataUpdater->run( $oTitle );
	}

	/**
	 * Standard return object
	 * Every task should return this!
	 * @return Standard
	 */
	protected function makeStandardReturn() {
		return new Standard();
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
	protected function logTaskAction( $sAction, $aParams, $aOptions = [], $bDoPublish = false ) {
		$aOptions += [
			'performer' => null,
			'target' => null,
			'timestamp' => null,
			'relations' => null,
			'comment' => null,
			'deleted' => null,
			'publish' => null,
			// To allow overriding of class default
			'type' => null
		];

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

		if ( $sType === null ) {
			// Not set on class, not set as call option
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
		return parent::getAllowedParams() + [
			'task' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => $this->oTasksSpec->getTaskNames(),
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-task-param-task',
				ApiBase::PARAM_HELP_MSG_PER_VALUE => $this->makeTaskHelpMessages()
			],
			'taskData' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '{}',
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-task-param-taskdata',
			],
			'context' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '{}',
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-task-param-context',
			],
			'schema' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-task-param-schema',
			],
			'examples' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ApiBase::PARAM_HELP_MSG => 'apihelp-bs-task-param-examples',
			]
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
		// Unfortunately there is no way to register custom types for parameters
		if ( in_array( $paramName, [ 'taskData', 'context' ] ) ) {
			$value = FormatJson::decode( $value );
			if ( empty( $value ) ) {
				return new stdClass();
			}
		}
		return $value;
	}

	/**
	 * Returns the basic description for this module
	 * @return array
	 */
	public function getDescription() {
		return [
			'BSApiTasksBase: This should be implemented by subclass'
		];
	}

	/**
	 * Returns the basic example
	 * @return array
	 */
	public function getExamples() {
		$aTaskNames = $this->oTasksSpec->getTaskNames();
		return [
			'api.php?action=' . $this->getModuleName() . '&task=' . $aTaskNames[0]
			. '&taskData={someKey:"someValue",isFalse:true}',
		];
	}

	/**
	 *
	 * @param string $sTask
	 * @return mixed bool|null if requested task not in list
	 * true if allowed
	 * true if permission list is empty
	 * false if not found in permission table of current user -> set in permission manager, group based
	 */
	public function checkTaskPermission( $sTask ) {
		$taskPermissions = $this->getTaskPermissions( $sTask );

		if ( $taskPermissions === false ) {
			return null;
		}
		if ( empty( $taskPermissions ) ) {
			return true;
		}
		// lookup permission for given task
		foreach ( $taskPermissions as $sPermission ) {
			// check if user have needed permission
			$isAllowed = $this->services->getPermissionManager()->userHasRight(
				$this->getUser(),
				$sPermission
			);
			if ( $isAllowed ) {
				continue;
			}
			// TODO: Reflect permission in error message
			return false;
		}

		return true;
	}

	/**
	 * Check user permisson on each task and return boolean array like "taskName" => true/false
	 * This can be used to show / hide ui elements
	 *
	 * @param array $oTaskData can be empty, default param for task
	 * @return array Elements of $oTasks with boolean attributes for grant / deny on each task
	 * provided by called api-class
	 */
	public function task_getUserTaskPermissions( $oTaskData ) {
		$oResponse = $this->makeStandardReturn();

		$aTaskPermissions = $this->getRequiredTaskPermissions();
		$arrReturn = [];
		foreach ( $aTaskPermissions as $sTask => $val ) {
			$arrReturn[$sTask] = $this->checkTaskPermission( $sTask );
		}

		$oResponse->payload = $arrReturn;
		$oResponse->success = true;

		return $oResponse;
	}

	/**
	 * Returns an array of tasks and their required permissions
	 * array('taskname' => array('read', 'edit'))
	 * @return string[][]
	 */
	protected function getRequiredTaskPermissions() {
		return [];
	}

	/**
	 * NOT IMPLEMENTED YET
	 * Use ParamProcessor to validate taskData params
	 * @param string $sTask
	 * @param stdClass $oTaskData
	 * @return stdClass - Standard return
	 */
	protected function validateTaskData( $sTask, $oTaskData ) {
		$aDefinitions = $this->oTasksSpec->getTaskDataDefinition( $sTask );
		$oReturn = $this->makeStandardReturn();
		if ( $aDefinitions === false ) {
			return $oReturn;
		}
		// TODO: Use ParamProcessor to validate params defined by
		return $oReturn;
	}

	/**
	 * General protection
	 * @return string
	 */
	public function needsToken() {
		if ( $this->isTaskDataSchemaCall() || $this->isTaskDataExamplesCall() ) {
			return false;
		}

		return 'csrf';
	}

	/**
	 * Set to false for all read modules
	 *
	 * @return bool
	 */
	public function isWriteMode() {
		try {
			$params = $this->extractRequestParams();
			if ( !isset( $params['task'] ) ) {
				return true;
			}
			$task = $params['task'];
			if ( in_array( $task, $this->aReadTasks ) ) {
				return false;
			}
		} catch ( ApiUsageException $ex ) {
			return true;
		}

		return true;
	}

	/**
	 * Returns an array of global tasks and their required permissions
	 * array( 'taskname' => array('read', 'edit') )
	 * @return array
	 */
	protected function getGlobalRequiredTaskPermissions() {
		return [
			'getUserTaskPermissions' => [ 'read' ]
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function makeTaskHelpMessages() {
		$aMessages = [];
		$aUrlParams = [
			'path' => wfScript( 'api' )
		];

		$urlUtils = $this->services->getUrlUtils();
		foreach ( $this->oTasksSpec->getTaskNames() as $sTaskName ) {
			$aMessages[$sTaskName] = [
				'bs-api-task-taskdata-help',
				$urlUtils->expand( $urlUtils->assemble( $aUrlParams + [
					'query' => wfArrayToCgi( [
						'action' => $this->getModuleName(),
						'task' => $sTaskName,
						'schema' => 1
					] )
				] ) ),
				$urlUtils->expand( $urlUtils->assemble( $aUrlParams + [
					'query' => wfArrayToCgi( [
						'action' => $this->getModuleName(),
						'task' => $sTaskName,
						'examples' => 1
					] )
				] ) )
			];
		}

		return $aMessages;
	}

	/**
	 *
	 * @param string $sTaskName
	 */
	protected function returnTaskDataSchema( $sTaskName ) {
		$this->getResult()->addValue(
			null,
			'schema',
			$this->oTasksSpec->getSchema( $sTaskName )
		);
	}

	/**
	 *
	 * @param string $sTaskName
	 */
	protected function returnTaskDataExamples( $sTaskName ) {
		$this->getResult()->addValue(
			null,
			'examples',
			$this->oTasksSpec->getExamples( $sTaskName )
		);
	}

	/**
	 * @return \BSApiFormatJson
	 */
	public function getCustomPrinter() {
		if ( $this->isTaskDataSchemaCall() || $this->isTaskDataExamplesCall() ) {
			return new BSApiFormatJson( $this->getMain(), 'jsonfm' );
		}

		return parent::getCustomPrinter();
	}

	/**
	 * @return bool
	 */
	protected function isTaskDataSchemaCall() {
		return $this->getRequest()->getVal( 'schema', null ) !== null;
	}

	/**
	 * @return bool
	 */
	protected function isTaskDataExamplesCall() {
		return $this->getRequest()->getVal( 'examples', null ) !== null;
	}

	/**
	 * Gets a default primary DB connection object
	 * @return IDatabase
	 */
	protected function getDB() {
		if ( !isset( $this->mPrimaryDB ) ) {
			$this->mPrimaryDB = $this->services->getDBLoadBalancer()
				->getConnection( DB_PRIMARY, 'api' );
		}

		return $this->mPrimaryDB;
	}

	protected function dieWithPermissionError() {
		$this->dieWithError( 'apierror-permissiondenied-generic', 'permissiondenied' );
	}

	/**
	 *
	 * @param string $task
	 * @param stdClass $taskData
	 */
	protected function checkTaskPermissionsAgainstTaskDataTitles( $task, $taskData ) {
		$titleParamResolver = $this->utilityFactory->getTitleParamsResolver( (array)$taskData );
		$titlesToTest = $titleParamResolver->resolve();
		$permissionsToTest = $this->getTaskPermissions( $task );
		$pm = $this->services->getPermissionManager();
		foreach ( $titlesToTest as $title ) {
			foreach ( $permissionsToTest as $permission ) {
				if ( !$pm->userCan( $permission, $this->getUser(), $title ) ) {
					$this->dieWithPermissionError();
				}
			}
		}
	}

	/**
	 * @param string $task
	 * @return array|false
	 */
	private function getTaskPermissions( $task ) {
		$taskPermissions = array_merge(
			$this->getRequiredTaskPermissions(),
			$this->getGlobalRequiredTaskPermissions()
		);
		if ( !isset( $taskPermissions[$task] ) ) {
			return false;
		}

		return $taskPermissions[$task];
	}

}
