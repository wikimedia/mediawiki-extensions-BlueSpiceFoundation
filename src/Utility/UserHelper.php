<?php

namespace BlueSpice\Utility;

use LogicException;
use MediaWiki\Context\RequestContext;
use MediaWiki\User\User;

class UserHelper {

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @param User|null $user
	 * @return UserHelper
	 * @throws LogicException
	 */
	public function __construct( ?User $user = null ) {
		$this->user = $user;
		if ( $this->user ) {
			return;
		}
		$this->user = RequestContext::getMain()->getUser();
		if ( !$this->user ) {
			throw new LogicException( 'User is required for UserHelper' );
		}
	}

	/**
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return empty( $this->user->getRealName() )
			? $this->user->getName()
			: $this->user->getRealName();
	}
}
