<?php

namespace BlueSpice\Hook\HtmlPageLinkRendererEnd;

use HtmlArmor;
use MediaWiki\Html\Html;
use MediaWiki\Title\Title;

class AddDataUserName extends \BlueSpice\Hook\HtmlPageLinkRendererEnd {

	protected function skipProcessing() {
		// this is a bit hacky but without the parser test for extension cite
		// may fail, as it checks for the equality of the complete parserd html
		// string, we modify here. We use our own test to verify that this code
		// works
		if ( defined( 'MW_PHPUNIT_TEST' ) && !defined( 'BS_ADD_DATA_USER_NAME_TEST' ) ) {
			return true;
		}
		if ( $this->target->isExternal() ) {
			return true;
		}
		if ( $this->target->getNamespace() !== NS_USER ) {
			return true;
		}
		$title = Title::newFromLinkTarget( $this->target );
		if ( !$title || !$title->canExist() ) {
			return true;
		}
		if ( $title->isSubpage() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$services = $this->getServices();
		$user = $services->getUserFactory()->newFromName( $this->target->getText() );

		if ( !$user ) {
			// in rare cases $this->target->getText() returns '127.0.0.1' which
			// results in 'false' in User::newFromName
			return true;
		}

		$text = HtmlArmor::getHtml( $this->text );
		// $text = "<bdi>WikiSysop</bdi>" - we must clean it for comparison
		$bdiWrapped = strpos( $text, '<bdi>' ) === 0;
		$text = strip_tags( $text );

		if ( $user->getName() === $text && $this->getConfig()->get( 'UseRealNameForUserLinks' ) ) {
			$this->text =
				$this->getServices()->getService( 'BSUtilityFactory' )->getUserHelper(
					$user
				)->getDisplayName();

			if ( $bdiWrapped ) {
				$this->text = new HtmlArmor(
					Html::element( 'bdi', [], $this->text )
				);
			}
		}

		$this->attribs['data-bs-username'] = $user->getName();
		return true;
	}
}
