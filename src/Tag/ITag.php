<?php

namespace BlueSpice\Tag;

interface ITag {
	/**
	 * @return string[]
	 */
	public function getTagNames();

	/**
	 * @param mixed $processedInput
	 * @param array $processedArgs
	 * @param \Parser $parser
	 * @param \PPFrame $frame
	 *
	 * @return IHandler
	 */
	public function getHandler( $processedInput, array $processedArgs, \Parser $parser, \PPFrame $frame );

	/**
	 * @return string
	 */
	public function getContainerElementName();

	/**
	 * @return boolean
	 */
	public function needsDisabledParserCache();

	/**
	 * @return boolean
	 */
	public function needsParsedInput();

	/**
	 * @return boolean
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
