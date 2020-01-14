<?php

namespace BlueSpice\Template;

use BlueSpice\ITemplateParser;
use BlueSpice\Services;
use BlueSpice\Utility\TemplateHelper;

class Mustache extends \BlueSpice\Template {
	const FILE_EXTENTION = 'mustache';

	/**
	 *
	 * @param Services $services
	 * @param string $alias
	 * @param TemplateHelper|null $templateHelper
	 * @param TemplateParser|null $templateParser
	 * @return Template
	 */
	public static function factory( Services $services, $alias,
		TemplateHelper $templateHelper = null,
		ITemplateParser $templateParser = null ) {
		if ( !$templateHelper ) {
			$templateHelper = $services->getBSUtilityFactory()->getTemplateHelper();
		}
		if ( !$templateParser ) {
			$templateParser = new \BlueSpice\TemplateParser(
				$templateHelper->getTemplateDirFromName( $alias )
			);
		}
		return new static( $alias, $templateHelper, $templateParser );
	}
}
