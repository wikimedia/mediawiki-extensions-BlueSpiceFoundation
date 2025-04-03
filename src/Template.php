<?php

namespace BlueSpice;

use BlueSpice\Utility\TemplateHelper;
use MediaWiki\MediaWikiServices;

abstract class Template implements ITemplate {
	/**
	 *
	 * @var string
	 */
	protected $alias = null;

	/**
	 *
	 * @var TemplateHelper
	 */
	protected $templateHelper = null;

	/**
	 *
	 * @var ITemplateParser
	 */
	protected $templateParser = null;

	/**
	 *
	 * @param string $alias
	 * @param TemplateHelper $templateHelper
	 * @param ITemplateParser $templateParser
	 */
	protected function __construct( $alias, TemplateHelper $templateHelper,
		ITemplateParser $templateParser ) {
		$this->alias = $alias;
		$this->templateHelper = $templateHelper;
		$this->templateParser = $templateParser;
	}

	/**
	 *
	 * @param MediaWikiServices $services
	 * @param string $alias
	 * @param TemplateHelper|null $templateHelper
	 * @param ITemplateParser|null $templateParser
	 * @return Template
	 */
	abstract public static function factory( MediaWikiServices $services, $alias,
		?TemplateHelper $templateHelper = null,
		?ITemplateParser $templateParser = null );

	/**
	 *
	 * @param array $args
	 * @param array $scopes
	 * @return string Rendered template
	 */
	public function process( array $args = [], array $scopes = [] ) {
		return $this->templateParser->processTemplate(
			$this->getFileName(),
			$args,
			$scopes
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getFilePath() {
		$dir = $this->templateHelper->getTemplateDirFromName( $this->alias );
		return $dir . "/" . $this->getFileName() . "." . static::FILE_EXTENTION;
	}

	/**
	 *
	 * @return string
	 */
	protected function getFileName() {
		$parts = explode( TemplateHelper::SEPARATOR, $this->alias );
		return array_pop( $parts );
	}
}
