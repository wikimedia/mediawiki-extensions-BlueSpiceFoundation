<?php

namespace BlueSpice\Tests;

abstract class BSApiTasksTestBase extends BSApiTestCase {

	abstract protected function getModuleName();

	protected function setUp() {
		parent::setUp();

		$this->doLogin();
	}

	protected function executeTask( $taskName, $taskData ) {
		$results = $this->doApiRequestWithToken( [
			'action' => $this->getModuleName(),
			'task' => $taskName,
			'taskData' => \FormatJson::encode( $taskData )
		] );

		return (object)$results[0];
	}
}
