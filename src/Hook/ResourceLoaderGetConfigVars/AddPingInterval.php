<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddPingInterval extends ResourceLoaderGetConfigVars {

	protected function doProcess() {
		$this->vars['bsgPingInterval'] = $this->getConfig()->get(
			'PingInterval'
		);
		return true;
	}

}
