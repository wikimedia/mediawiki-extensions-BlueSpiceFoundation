<?php

namespace BlueSpice\Hook\LinkerMakeMediaLinkFile;

class AddDataAttributes extends \BlueSpice\Hook\LinkerMakeMediaLinkFile {

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
		$this->attribs['data-bs-title'] = $this->title->getPrefixedText();

		if( $this->file instanceof \File ) {
			$this->attribs['data-bs-filename'] = $this->file->getName();
			$this->attribs['data-bs-filetimestamp']
				= $this->file->getTimestamp();
		}
		else {
			$attribs['data-bs-filename'] = $this->title->getText();
			$attribs['data-bs-filetimestamp'] = '';
		}
	}

}
