<?php

namespace BlueSpice\Html\OOUI;

class ItemWidget extends \OOUI\Widget {

	/**
	 *
	 * @param array $config
	 * @return null
	 */
	protected function getInputElement( $config ) {
		return null;
	}

	/**
	 *
	 * @return string
	 */
	public function getJavaScriptClassName() {
		return "OO.ui.ItemWidget";
	}
}
