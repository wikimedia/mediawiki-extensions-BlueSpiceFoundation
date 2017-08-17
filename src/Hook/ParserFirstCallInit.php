<?php

namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class ParserFirstCallInit extends Hook {

	/**
	 *
	 * @var \Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @param \Parser $parser
	 * @return boolean
	 */
	public static function callback( $parser ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$parser
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \Parser $parser
	 */
	public function __construct( $context, $config, $parser ) {
		parent::__construct( $context, $config );

		$this->parser = $parser;
	}
}