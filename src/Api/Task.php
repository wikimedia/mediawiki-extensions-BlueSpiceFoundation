<?php

namespace BlueSpice\Api;

use Status;
use FormatJson;
use BlueSpice\ITask;
use BlueSpice\IPermissionChecker;
use BlueSpice\Api;
use BlueSpice\Api\Task\StatusConverter;
use BlueSpice\ParamProcessor\Processor;
use BlueSpice\ParamProcessor\Options;
use Exception;

class Task extends Api {
	const PARAM_TASK = 'task';
	const PARAM_TASK_DATA = 'taskData';
	const PARAM_CONTEXT = 'context';
	const PARAM_SCHEMA = 'schema';
	const PARAM_EXAMPLES = 'examples';
	const PARAM_FORMAT = 'format';

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
		$converter = new StatusConverter( $this, $status );
		$converter->convert();
	}

	/**
	 *
	 * @param ITask $task
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
				static::PARAM_REQUIRED => true,
				static::PARAM_TYPE => 'string',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-task',
			],
			static::PARAM_TASK_DATA => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-taskdata',
			],
			static::PARAM_CONTEXT => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_DFLT => '{}',
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-context',
			],
			static::PARAM_SCHEMA => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-schema',
			],
			static::PARAM_EXAMPLES => [
				static::PARAM_TYPE => 'string',
				static::PARAM_REQUIRED => false,
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-examples',
			],
			static::PARAM_FORMAT => [
				static::PARAM_DFLT => 'json',
				static::PARAM_TYPE => [ 'json', 'jsonfm' ],
				static::PARAM_HELP_MSG => 'apihelp-bs-task-param-format',
			]
		];
	}

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
		return $this->getServices()->getBSTaskFactory()->get(
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
