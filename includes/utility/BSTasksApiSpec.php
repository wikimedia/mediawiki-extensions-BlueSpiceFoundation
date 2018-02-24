<?php

class BSTasksApiSpec {
	protected $aInitialConfig = [];

	protected $aTaskNames = [];

	public function __construct( $aTasks ) {
		$this->aInitialConfig = $aTasks;
		$this->extractTaskNames();
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTaskNames() {
		return $this->aTaskNames;
	}

	/**
	 *
	 * @param string $sTaskName
	 * @return array the spec
	 */
	public function getTaskSpec( $sTaskName ) {
		return isset( $this->aInitialConfig[$sTaskName] )
			? $this->aInitialConfig[$sTaskName]
			: [];
	}

	/**
	 *
	 * @param string $sTaskName
	 * @return array
	 */
	public function getTaskDataDefinition( $sTaskName ) {
		$aTaskSpec = $this->getTaskSpec( $sTaskName );
		return isset( $aTaskSpec['params'] ) ? $aTaskSpec['params'] : [];
	}

	protected function extractTaskNames() {
		foreach( $this->aInitialConfig as $mKey => $mValue ) {
			if( is_string( $mKey ) && is_array( $mValue ) ) {
				$this->aTaskNames[] = $mKey;
			}
			elseif( is_int( $mKey ) && is_string( $mValue ) ) {
				$this->aTaskNames[] = $mValue;
			}
			else {
				throw new MWException( 'Unsupported TaskAPI spec format!' );
			}
		}
	}

	/**
	 *
	 * @param string $sTaskName
	 * @return array
	 */
	public function getSchema( $sTaskName ) {
		return [
			'task' => [
				'description' => wfMessage( 'apihelp-bs-task-param-task' )->plain(),
				'type' => 'string'
			],
			'taskData' => [
				'type' => 'object',
				'properties' => $this->makeTaskDataProperties( $sTaskName )
			]
		];
	}

	protected function makeTaskDataProperties( $sTaskName ) {
		$aProps = [];
		if( !isset( $this->aInitialConfig[$sTaskName]['params'] ) ) {
			return $aProps;
		}
		foreach( $this->aInitialConfig[$sTaskName]['params'] as $sParamName => $aParamDefinition ) {
			$aProps[$sParamName] = [];
			$sDesc = isset( $aParamDefinition['desc'] ) ? $aParamDefinition['desc'] : '';
			if( !empty( $sDesc ) ) { //Explicitly set?
				$oMsg = wfMessage( $sDesc );
				$aProps[$sParamName]['description'] = $oMsg->exists() ? $oMsg->plain() : $sDesc;
			}
			else { //Maybe there is a generic description available
				$oMsgDefault = wfMessage( 'bs-api-task-taskData-'. str_replace( '_', '-',  $sParamName ) ); // 'page_id' => 'bs-api-task-taskData-page-id'
				if( $oMsgDefault->exists() ) {
					$aProps[$sParamName]['description'] = $oMsgDefault->plain();
				}
			}

			$aProps[$sParamName] += [
				'type' => isset( $aParamDefinition['type'] ) ? $aParamDefinition['type'] : 'string',
				'required' => isset( $aParamDefinition['required'] ) ? $aParamDefinition['required'] : false,
			];

			if( isset( $aParamDefinition['default'] ) ) {
				$aProps[$sParamName]['default'] = $aParamDefinition['default'];
			}
			//TODO: Describe sub structures (like array-of-objects) in more detail
		}

		return $aProps;
	}

	/**
	 *
	 * @param strnig $sTaskName
	 * @return array
	 */
	public function getExamples( $sTaskName ) {
		if( !isset( $this->aInitialConfig[$sTaskName]['examples'] ) ) {
			return [];
		}
		$aExamples = [];
		foreach( $this->aInitialConfig[$sTaskName]['examples'] as $aExampleTaskData ) {
			$aExamples[] = [
				'task' => $sTaskName,
				'taskData' => $aExampleTaskData
			];
		}

		return $aExamples;
	}
}
