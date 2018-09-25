<?php

namespace BlueSpice;

interface IPageTool {

	/**
	 * @return string
	 */
	public function getHtml();

	/**
	 * @return string[]
	 */
	public function getPermissions();

	/**
	 * @return int
	 */
	public function getPosition();
}
