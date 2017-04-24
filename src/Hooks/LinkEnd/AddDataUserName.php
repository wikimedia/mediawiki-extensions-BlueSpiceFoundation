<?php

namespace BlueSpice\Hooks\LinkEnd;

class AddDataUserName extends \BlueSpice\Hooks\LinkEnd {
	protected function doProcess() {
		if( $this->target->getNamespace() !== NS_USER || $this->target->isSubpage() ) {
			return true;
		}

		$user = \User::newFromName( $this->target->getText() );

		if( $this->target->getText() === $this->html ) {
			$this->html = htmlspecialchars(
				\BsCore::getUserDisplayName( $user )
			);
		}

		$this->attribs['data-bs-username'] = $user->getName();
	}
}