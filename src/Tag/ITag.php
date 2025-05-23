<?php

namespace BlueSpice\Tag;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

/**
 * @deprecated Use mediawiki-component-generictaghandler instead
 */
interface ITag {
	/**
	 * @return string[]
	 */
	public function getTagNames();

	/**
	 * @param mixed $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 *
	 * @return IHandler
	 */
	public function getHandler( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame );

	/**
	 * @return string
	 */
	public function getContainerElementName();

	/**
	 * @return bool
	 */
	public function needsDisabledParserCache();

	/**
	 * @return bool
	 */
	public function needsParsedInput();

	/**
	 * @return bool
	 */
	public function needsParseArgs();

	/**
	 * @return string[]
	 */
	public function getResourceLoaderModules();

	/**
	 * @return string[]
	 */
	public function getResourceLoaderModuleStyles();

	/**
	 * @return MarkerType
	 */
	public function getMarkerType();

	/**
	 * @return \BlueSpice\ParamProcessor\IParamDefinition
	 */
	public function getInputDefinition();

	/**
	 * @return \BlueSpice\ParamProcessor\IParamDefinition[]
	 */
	public function getArgsDefinitions();
}
