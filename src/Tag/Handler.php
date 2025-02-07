<?php

namespace BlueSpice\Tag;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

abstract class Handler implements IHandler {

	/**
	 *
	 * @var string
	 */
	protected $processedInput = '';

	/**
	 *
	 * @var array
	 */
	protected $processedArgs = [];

	/**
	 *
	 * @var Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var PPFrame
	 */
	protected $frame = null;

	/**
	 *
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 */
	public function __construct( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame ) {
		$this->processedInput = $processedInput;
		$this->processedArgs = $processedArgs;
		$this->parser = $parser;
		$this->frame = $frame;
	}
}
