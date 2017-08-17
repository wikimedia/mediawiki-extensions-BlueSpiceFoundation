<?php

namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BeforePageDisplay extends Hook {

	/**
	 *
	 * @var \OutputPage
	 */
	protected $out = null;

	/**
	 *
	 * @var \Skin
	 */
	protected $skin = null;

	/**
	 *
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 * @return boolean
	 */
	public static function callback( $out, $skin  ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$out,
			$skin
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public function __construct( $context, $config, $out, $skin ) {
		parent::__construct( $context, $config );

		$this->out = $out;
		$this->skin = $skin;
	}
}