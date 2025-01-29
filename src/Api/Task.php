<?php

namespace BlueSpice\Api;

use BlueSpice\Api;
use BlueSpice\Api\Task\StatusConverter;
use BlueSpice\IPermissionChecker;
use BlueSpice\ITask;
use BlueSpice\ParamProcessor\Options;
use BlueSpice\ParamProcessor\Processor;
use Exception;
use MediaWiki\Json\FormatJson;
use MediaWiki\Status\Status;
use stdClass;
use Wikimedia\ParamValidator\ParamValidator;

class Task extends Api {
	public const PARAM_TASK = 'task';
	public const PARAM_TASK_DATA = 'taskData';
	public const PARAM_CONTEXT = 'context';
	public const PARAM_SCHEMA = 'schema';
	public const PARAM_EXAMPLES = 'examples';
	public const PARAM_FORMAT = 'format';

	public function execute() {
		$status = Status::newGood();
		try {
			$task = $this->getTask( $this->getParameter( static::PARAM_TASK ) );
			$status->merge( $this->getProcessedArgs( $task ), true );
			$args = [];
			if ( $status->isOK() ) {
				$args = $status->getValue();
			}
			$status = $task->execute( $args, $status );
		} catch ( Exception $e ) {
			$status->fatal( $e->getMessage() );
		}

		$api = $this;
		$converter = new StatusConverter( $api, $status );
		$converter->convert();
	}

	/**
	 *
	 * @param ITask $task
	 * @param array $processedArgs
	 * @return Status
	 */
	protected function getProcessedArgs( ITask $task, array $processedArgs = [] ) {
		$status = Status::newGood();
		$rawArgs = $this->getParameter( static::PARAM_TASK_DATA );

		foreach ( $task->getArgsDefinitions() as $paramDefinition ) {
			$paramDefinition->setMessage(
				$this->msg(
					'apihelp-bs-task-param-taskdata',
					$paramDefinition->getName()
				)->plain()
			);
			$options = $this->getParamProcessorOptions();
			$options->setName( 'arg-' . $paramDefinition->getName() );
			$processor = $this->getParamProcessor( $options );
			$localArgs = [];
			$names = array_merge(
				[ $paramDefinition->getName() ],
				$paramDefinition->getAliases()
			);

			foreach ( $names as $name ) {
				if ( isset( $rawArgs->{$name} ) ) {
					$localArgs[$name] = $rawArgs->{$name};
				}
			}

			$processor->setParameters( $localArgs, [ $paramDefinition ] );
			$result = $processor->processParameters();

			foreach ( $result->getErrors() as $error ) {
				$status->error( $error->getMessage() );
			}

			foreach ( $result->getParameters() as $processedParam ) {
				$processedArgs[$processedParam->getName()]
					= $processedParam->getValue();
			}
		}
		if ( $status->isOK() ) {
			$status->merge( Status::newGood( $processedArgs ), true );
		}
		return $status;
	}

	/**
	 *
	 * @param Options $options
	 * @return Processor
	 */
	protected function getParamProcessor( Options $options ) {
		return Processor::newFromOptions( $options );
	}

	/**
	 *
	 * @return Options
	 */
	protected function getParamProcessorOptions() {
		return new Options();
	}

	/**
	 * Returns an array of allowed parameters
	 * @return array
	 */
	protected function getAllowedParams() {
		return [
			static::PARAM_TASK => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => 'string',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-task',
			],
			static::PARAM_TASK_DATA => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-taskdata',
			],
			static::PARAM_CONTEXT => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-context',
			],
			static::PARAM_SCHEMA => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-schema',
			],
			static::PARAM_EXAMPLES => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-examples',
			],
			static::PARAM_FORMAT => [
				ParamValidator::PARAM_DEFAULT => 'json',
				ParamValidator::PARAM_TYPE => [ 'json', 'jsonfm' ],
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-format',
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
		$value = parent::getParameterFromSettings(
			$paramName,
			$paramSettings,
			$parseLimit
		);
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
	 * @param string $taskName
	 * @return ITask
	 */
	protected function getTask( $taskName ) {
		return $this->services->getService( 'BSTaskFactory' )->get(
			$taskName,
			$this->getContext(),
			$this->makePermissionChecker()
		);
	}

	/**
	 *
	 * @return IPermissionChecker
	 */
	protected function makePermissionChecker() {
		return new \BlueSpice\PermissionChecker\Title();
	}
}
