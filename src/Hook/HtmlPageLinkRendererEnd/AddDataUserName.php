<?php

namespace BlueSpice\Hook\HtmlPageLinkRendererEnd;

class AddDataUserName extends \BlueSpice\Hook\HtmlPageLinkRendererEnd {

	protected function skipProcessing() {
		if( $this->target->isExternal() ) {
			return true;
		}
		if( $this->target->getNamespace() !== NS_USER ) {
			return true;
		}
		$title = \Title::newFromLinkTarget( $this->target );
		if( !$title ) {
			return true;
		}
		if( $title->isSubpage() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$user = \User::newFromName( $this->target->getText() );

		if( !$user ) {
			//in rare cases $this->target->getText() returns '127.0.0.1' which
			//results in 'false' in User::newFromName
			return true;
		}

		$text = \HtmlArmor::getHtml( $this->text );
		if( $user->getName() === $text ) {
			$this->text = new \HtmlArmor(
				\BsUserHelper::getUserDisplayName( $user )
			);
		}

		$this->attribs['data-bs-username'] = $user->getName();
		return true;
	}
}
