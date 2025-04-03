<?php

namespace BlueSpice;

use MediaWiki\Status\Status;

interface ITask extends IParamProvider {

	/**
	 * @param array $params
	 * @param Status|null $status
	 * @return Status
	 */
	public function execute( array $params = [], ?Status $status = null );

	/**
	 * @return string[]
	 */
	public function getTaskPermissions();
}
