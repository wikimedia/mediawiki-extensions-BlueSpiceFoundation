<?php
namespace BlueSpice\Permission;

use MediaWiki\Message\Message;

interface IRole {
	/**
	 * @param PermissionRegistry $permissionRegistry
	 * @return IRole
	 */
	public static function factory( PermissionRegistry $permissionRegistry );

	/**
	 * Returns permission array for the role
	 * @return array
	 */
	public function getPermissions();

	/**
	 * Returns the name of the role
	 * @return string
	 */
	public function getName();

	/**
	 * Adds single permission to the role
	 * @param string $permission
	 */
	public function addPermission( $permission );

	/**
	 * Removes single permission from the role
	 * @param string $permission
	 */
	public function removePermission( $permission );

	/**
	 * @return string[]
	 */
	public function getRequiredPermissions();

	/**
	 * Returns a relative index of priviledge. Higher numbers are more priviledged
	 * @return int
	 */
	public function getPrivilegeLevel();

	/**
	 * @return Message
	 */
	public function getLabel(): Message;
}
