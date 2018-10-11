<?php

namespace BlueSpice\Hook\HtmlPageLinkRendererEnd;

class AddDataTitle extends \BlueSpice\Hook\HtmlPageLinkRendererEnd {

	protected function skipProcessing() {
		if( $this->target->isExternal() ) {
			return true;
		}
		if( empty( $this->target->getDBkey() ) ) {
			//not a real target (i.e links from the cite extension)
			return true;
		}
		return false;
	}

	protected function doProcess() {
		//We add the original title to a link. This may be the same content as
		//"title" attribute, but it doesn't have to. I.e. in red links
		$this->attribs['data-bs-title'] = $this->target->getDBkey();

		if( $this->target->getNamespace() === NS_FILE ) {
			$this->attribs['data-bs-filename'] = $this->target->getText();
			$this->attribs['data-bs-filetimestamp'] = '';
		}

		return true;
	}
}
