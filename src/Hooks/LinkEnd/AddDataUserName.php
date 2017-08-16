<?php

namespace BlueSpice\Hooks\LinkEnd;

class AddDataUserName extends \BlueSpice\Hooks\LinkEnd {
	protected function doProcess() {
		if( $this->target->getNamespace() !== NS_USER || $this->target->isSubpage() ) {
			return true;
		}

		$user = \User::newFromName( $this->target->getText() );
		if( !$user ) {
			//in rare cases $this->target->getText() returns '127.0.0.1' which
			//results in 'false' in User::newFromName
			return true;
		}

		if( $this->target->getText() === $this->html ) {
			$this->html = htmlspecialchars(
				\BsUserHelper::getUserDisplayName( $user )
			);
		}

		$this->attribs['data-bs-username'] = $user->getName();
	}
}