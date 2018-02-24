<?php

namespace BlueSpice;

interface ITask {

	/**
	 * @return \Status
	 */
	public function execute();
}
