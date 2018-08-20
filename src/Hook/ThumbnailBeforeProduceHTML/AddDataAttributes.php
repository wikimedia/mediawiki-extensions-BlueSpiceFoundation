<?php

namespace BlueSpice\Hook\ThumbnailBeforeProduceHTML;

class AddDataAttributes extends \BlueSpice\Hook\ThumbnailBeforeProduceHTML {

	protected function doProcess() {
		$this->linkAttribs['data-bs-title']
			= $this->thumbnail->getFile()->getTitle()->getPrefixedDBKey();
		$this->linkAttribs['data-bs-filetimestamp']
			= $this->thumbnail->getFile()->getTimestamp();
	}

}
