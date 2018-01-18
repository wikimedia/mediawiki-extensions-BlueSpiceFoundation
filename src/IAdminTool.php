<?php

namespace BlueSpice;

interface IAdminTool {

	/**
	 * @return string
	 */
	public function getURL();

	/**
	 * @return \Message
	 */
	public function getDescription();

	/**
	 * @return \Message
	 */
	public function getName();

	/**
	 * @return array
	 */
	public function getClasses();

	/**
	 * @return array
	 */
	public function getDataAttributes();

	/**
	 * @return array
	 */
	public function getPermissions();

}
