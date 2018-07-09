<?php

namespace BlueSpice;

interface IAlertProvider {

	//Keep in sync with `bs.alerts` constants
	const TYPE_SUCCESS = 'success';
	const TYPE_INFO = 'info';
	const TYPE_WARNING = 'warning';
	const TYPE_DANGER = 'danger';

	/**
	 * @return string
	 */
	public function getHTML();

	/**
	 * @return string One to the IAlertProvider::TYPE_* constants
	 */
	public function getType();
}
