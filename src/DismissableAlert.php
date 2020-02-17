<?php

namespace BlueSpice;

use OOUI\ButtonWidget;

abstract class DismissableAlert extends AlertProviderBase {

	/**
	 * @inheritDoc
	 */
	public function getHTML() {
		$this->skin->getOutput()->enableOOUI();

		$btn = new ButtonWidget( [
			'framed' => false,
			'title' => $this->skin->msg( 'bs-ooui-btn-dismiss-alert-title' )->text(),
			'icon' => 'close',
			'infusable' => true,
			'classes' => [ 'dismiss-btn', 'alert-top-right' ]
		] );

		return $btn . $this->getInnerHTML();
	}

	/**
	 * @return string
	 */
	abstract public function getInnerHTML();
}
