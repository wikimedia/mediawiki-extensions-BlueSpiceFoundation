<?php

namespace BlueSpice\Hook\ThumbnailBeforeProduceHTML;

class AddDataAttributes extends \BlueSpice\Hook\ThumbnailBeforeProduceHTML {

	protected function skipProcessing() {
		//this is a bit hacky but without the parser test for extension cite
		//may fail, as it checks for the equality of the complete parserd html
		//string, we modify here. TODO: Make own test, that verifies that this
		//code works
		if( defined( 'MW_PHPUNIT_TEST' ) ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$this->linkAttribs['data-bs-title']
			= $this->thumbnail->getFile()->getTitle()->getPrefixedDBKey();
		$this->linkAttribs['data-bs-filetimestamp']
			= $this->thumbnail->getFile()->getTimestamp();
	}

}
