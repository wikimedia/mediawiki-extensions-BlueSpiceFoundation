<?php

namespace BlueSpice\Permission\Lockdown\Module\Groups;

use MediaWiki\User\User;

interface ISubModule extends \BlueSpice\Permission\Lockdown\IModule {
	/**
	 * Returns the groups, that may get their permissions nullified
	 * @param User $user
	 * @return string[]
	 */
	public function getLockdownGroups( User $user );
}
