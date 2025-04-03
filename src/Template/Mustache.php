<?php

namespace BlueSpice\Template;

use BlueSpice\ITemplateParser;
use BlueSpice\Utility\TemplateHelper;
use MediaWiki\MediaWikiServices;

class Mustache extends \BlueSpice\Template {
	public const FILE_EXTENTION = 'mustache';

	/**
	 * @param MediaWikiServices $services
	 * @param string $alias
	 * @param TemplateHelper|null $templateHelper
	 * @param ITemplateParser|null $templateParser
	 * @return Template
	 */
	public static function factory( MediaWikiServices $services, $alias,
		?TemplateHelper $templateHelper = null,
		?ITemplateParser $templateParser = null ) {
		if ( !$templateHelper ) {
			$templateHelper = $services->getService( 'BSUtilityFactory' )->getTemplateHelper();
		}
		if ( !$templateParser ) {
			$templateParser = new \BlueSpice\TemplateParser(
				$templateHelper->getTemplateDirFromName( $alias )
			);
		}
		return new static( $alias, $templateHelper, $templateParser );
	}
}
