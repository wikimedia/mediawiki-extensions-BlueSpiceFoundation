<?php

namespace BlueSpice\Tests;

abstract class BSApiTasksTestBase extends BSApiTestCase {

	/**
	 * @return string
	 */
	abstract protected function getModuleName();

	protected function setUp() : void {
		parent::setUp();

		$this->doLogin();
	}

	/**
	 *
	 * @param string $taskName
	 * @param \stdClass $taskData
	 * @return \stdClass
	 */
	protected function executeTask( $taskName, $taskData ) {
		$results = $this->doApiRequestWithToken( [
			'action' => $this->getModuleName(),
			'task' => $taskName,
			'taskData' => \FormatJson::encode( $taskData )
		] );

		return (object)$results[0];
	}
}
