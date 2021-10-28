<?php

namespace BlueSpice;

use Status;
use Title;

interface ISecondaryDataUpdate {
	/**
	 * @param Title $title
	 * @return Status
	 */
	public function run( Title $title );
}
