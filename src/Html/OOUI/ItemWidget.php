<?php

namespace BlueSpice\Html\OOUI;

class ItemWidget extends \OOUI\Widget {

	protected function getInputElement( $config ) {
		return null;
	}

	public function getJavaScriptClassName() {
		return "OO.ui.ItemWidget";
	}
}
