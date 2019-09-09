<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddPingInterval extends ResourceLoaderGetConfigVars {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->vars['bsgPingInterval'] = $this->getConfig()->get(
			'PingInterval'
		);
		return true;
	}

}
