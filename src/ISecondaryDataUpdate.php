<?php

namespace BlueSpice;

use MediaWiki\Title\Title;
use Status;

interface ISecondaryDataUpdate {
	/**
	 * @param Title $title
	 * @return Status
	 */
	public function run( Title $title );
}
