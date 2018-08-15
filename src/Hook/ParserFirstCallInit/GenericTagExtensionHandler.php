<?php

namespace BlueSpice\Hook\ParserFirstCallInit;

/**
 * @deprecated since version 3.0.0 - BlueSpiceFoundationTagRegistry for
 * BSTagFactory should be used
 */
class GenericTagExtensionHandler extends \BlueSpice\Hook\ParserFirstCallInit {

	protected function doProcess() {
		$factory = $this->getServices()->getBSExtensionFactory();
		\BsGenericTagExtensionHandler::setupHandlers(
			$factory->getExtensions(),
			$this->parser
		);

		return true;
	}
}
