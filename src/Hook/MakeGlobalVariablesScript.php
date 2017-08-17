<?php

namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class MakeGlobalVariablesScript extends Hook {

	/**
	 *
	 * @var array
	 */
	protected $vars = [];

	/**
	 *
	 * @var \OutputPage
	 */
	protected $out = null;
	/**
	 *
	 * @param array$vars
	 * @param \OutputPage $out
	 * @return boolean
	 */
	public static function callback( &$vars, $out ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$vars,
			$out
		);
		return $hookHandler->process();
	}

	public function __construct( $context, $config, &$vars, $out ) {
		parent::__construct( $context, $config );

		$this->vars =& $vars;
		$this->out = $out;
	}
}