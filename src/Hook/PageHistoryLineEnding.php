<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class PageHistoryLineEnding extends Hook {

	/**
	 *
	 * @var \HistoryPager
	 */
	protected $history = null;

	/**
	 *
	 * @var \stdClass
	 */
	protected $row = null;

	/**
	 *
	 * @var string
	 */
	protected $s = '';

	/**
	 *
	 * @var array
	 */
	protected $classes = [];

	/**
	 *
	 * @var array
	 */
	protected $attribs = [];

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \HistoryPager $history
	 * @param \stdClass $row
	 * @param string $s
	 * @param array $classes
	 * @param array $attribs
	 */
	public function __construct( $context, $config, $history, &$row, &$s, &$classes, &$attribs ) {
		parent::__construct( $context, $config );

		$this->history = $history;
		$this->row =& $row;
		$this->s =& $s;
		$this->classes =& $classes;
		$this->attribs =& $attribs;
	}

	/**
	 *
	 * @param \HistoryPager $history
	 * @param \stdClass $row
	 * @param string $s
	 * @param string $classes
	 * @param array $attribs
	 * @return boolean
	 */
	public static function callback( $history, &$row, &$s, &$classes, &$attribs ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$history,
			$row,
			$s,
			$classes,
			$attribs
		);
		return $hookHandler->process();
	}
}
