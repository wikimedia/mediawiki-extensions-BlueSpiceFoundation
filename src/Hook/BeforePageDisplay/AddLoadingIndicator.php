<?php

namespace BlueSpice\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use Html;

class AddLoadingIndicator extends BeforePageDisplay {

	protected function doProcess() {
		$html = Html::openElement( 'div', [
			'class' => 'loader-indicator loading'
		] );
		$html .= Html::element( 'div', [
			'class' => 'loader-indicator-inner'
		] );
		$html .= Html::closeElement( 'div' );
		$this->out->addHTML( $html );
	}
}
