<?php

namespace BlueSpice\Hook\ResourceLoaderGetConfigVars;

use BlueSpice\Hook\ResourceLoaderGetConfigVars;

class AddPingVars extends ResourceLoaderGetConfigVars {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->vars['bsgPingInterval'] = $this->getConfig()->get(
			'PingInterval'
		);
		$this->vars['bsgPingOnInit'] = $this->getConfig()->get(
			'PingOnInit'
		);
		return true;
	}

}
