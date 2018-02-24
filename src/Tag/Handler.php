<?php

namespace BlueSpice\Tag;

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
	 * @var \Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var \PPFrame
	 */
	protected $frame = null;

	public function __construct( $processedInput, array $processedArgs, \Parser $parser, \PPFrame $frame ) {
		$this->processedInput = $processedInput;
		$this->processedArgs = $processedArgs;
		$this->parser = $parser;
		$this->frame = $frame;
	}
}
