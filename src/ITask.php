<?php

namespace BlueSpice;

use Status;
use BlueSpice\ParamProcessor\ParamDefinition;

interface ITask {

	/**
	 * @param array $params
	 * @param Status|null $status
	 * @return Status
	 */
	public function execute( array $params = [], Status $status = null );

	/**
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions();

	/**
	 * @return string[]
	 */
	public function getTaskPermissions();
}
