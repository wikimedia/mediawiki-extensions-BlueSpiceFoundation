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
	 * @retrun string
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
	 * @retrun MarkerType
	 */
	public function getMarkerType();

	/**
	 * @return \ParamProcessor\ParamDefinition
	 */
	public function getInputDefinition();

	/**
	 * @return \ParamProcessor\ParamDefinition[]
	 */
	public function getArgsDefinitions();
}