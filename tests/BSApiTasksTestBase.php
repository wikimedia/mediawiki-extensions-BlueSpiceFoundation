<?php

abstract class BSApiTasksTestBase extends ApiTestCase {

	abstract protected function getModuleName();

	protected function setUp() {
		parent::setUp();

		$this->doLogin();
	}

	protected function executeTask( $taskName, $taskData ) {
		global $wgGroupPermissions;
		$wgGroupPermissions['*']['read'] = true;
		$wgGroupPermissions['*']['writeapi'] = true;

		$results = $this->doApiRequestWithToken([
			'action' => $this->getModuleName(),
			'task' => $taskName,
			'taskData' => FormatJson::encode( $taskData )
		]);

		return (object)$results[0];
	}
}