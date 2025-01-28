<?php

namespace BlueSpice\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use MediaWiki\Html\Html;

class AddLoadingIndicator extends BeforePageDisplay {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$html = Html::openElement( 'div', [
			'class' => 'loader-indicator global loading'
		] );
		$html .= Html::element( 'div', [
			'class' => 'loader-indicator-inner'
		] );
		$html .= Html::closeElement( 'div' );
		$this->out->addHTML( $html );
		return true;
	}
}
