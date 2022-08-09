<?php
namespace BlueSpice\Content;

use Content;
use MediaWiki\Content\Renderer\ContentParseParams;
use ParserOutput;

abstract class EntityHandler extends \JsonContentHandler {

	/**
	 *
	 * @param string $modelId
	 */
	public function __construct( $modelId = '' ) {
		parent::__construct( $modelId );
	}

	/**
	 * @return string
	 */
	protected function getContentClass() {
		return "\\BlueSpice\\Content\\Entity";
	}

	/**
	 * @param Content $content
	 * @param ContentParseParams $cpoParams
	 * @param ParserOutput &$output The output object to fill (reference).
	 */
	protected function fillParserOutput(
		Content $content,
		ContentParseParams $cpoParams,
		ParserOutput &$output
	) {
		parent::fillParserOutput( $content, $cpoParams, $output );
	}
}
