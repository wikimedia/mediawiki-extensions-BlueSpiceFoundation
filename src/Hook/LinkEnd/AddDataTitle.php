<?php

namespace BlueSpice\Hook\LinkEnd;

class AddDataTitle extends \BlueSpice\Hook\LinkEnd {
	protected function doProcess() {
		//We add the original title to a link. This may be the same content as
		//"title" attribute, but it doesn't have to. I.e. in red links
		$this->attribs['data-bs-title'] = $this->target->getPrefixedDBkey();
		return true;
	}
}