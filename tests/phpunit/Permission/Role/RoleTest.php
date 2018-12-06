<?php

namespace BlueSpice\Tests\Permission\Role;

use PHPUnit\Framework\TestCase;
use BlueSpice\Permission\Role\Role;

class RoleTest extends TestCase {

	/**
	 * @covers BlueSpice\Permission\Role\Role::newFromNameAndPermissions
	 */
	public function testNewFromNameAndPermissions() {
		$role = Role::newFromNameAndPermissions( 'some_role_name' );

		$this->assertInstanceOf(
			'BlueSpice\Permission\Role\Role',
			$role
		);
	}

	/**
	 * @covers BlueSpice\Permission\Role\Role::getName
	 */
	public function testGetName() {
		$role = Role::newFromNameAndPermissions( 'some_role_name' );
		$this->assertEquals( 'some_role_name', $role->getName() );
	}

	/**
	 * @covers BlueSpice\Permission\Role\Role::getPermissions
	 */
	public function testGetPermissions() {
		$role = Role::newFromNameAndPermissions( 'some_role_name', [ 'A', 'B', 'C' ] );
		$this->assertEquals( [ 'A', 'B', 'C' ], $role->getPermissions() );
	}

	/**
	 * @covers BlueSpice\Permission\Role\Role::addPermission
	 */
	public function testAddPermission() {
		$role = Role::newFromNameAndPermissions( 'some_role_name', [ 'A', 'B', 'C' ] );
		$role->addPermission( 'D' );
		$this->assertEquals( [ 'A', 'B', 'C', 'D' ], $role->getPermissions() );
	}

	/**
	 * @covers BlueSpice\Permission\Role\Role::removePermission
	 */
	public function testRemovePermission() {
		$role = Role::newFromNameAndPermissions( 'some_role_name', [ 'A', 'B', 'C' ] );
		$role->removePermission( 'C' );
		$this->assertEquals( [ 'A', 'B' ], $role->getPermissions() );
	}
}
