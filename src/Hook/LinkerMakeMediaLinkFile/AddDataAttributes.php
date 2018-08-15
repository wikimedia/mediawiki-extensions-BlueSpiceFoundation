<?php

namespace BlueSpice\Hook\LinkerMakeMediaLinkFile;

class AddDataAttributes extends \BlueSpice\Hook\LinkerMakeMediaLinkFile {

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
