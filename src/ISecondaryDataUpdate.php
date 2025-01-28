<?php

namespace BlueSpice;

use MediaWiki\Status\Status;
use MediaWiki\Title\Title;

interface ISecondaryDataUpdate {
	/**
	 * @param Title $title
	 * @return Status
	 */
	public function run( Title $title );
}
