<?php

namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class SkinTemplateNavigation extends Hook {

	/**
	 *
	 * @var \SkinTemplate
	 */
	protected $sktemplate = null;

	/**
	 *
	 * @var array
	 */
	protected $links = [];

	/**
	 *
	 * @param \SkinTemplate $sktemplate
	 * @param array $links
	 * @return boolean
	 */
	public static function callback( &$sktemplate, &$links ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$sktemplate,
			$links
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \SkinTemplate $sktemplate
	 * @param array $links
	 */
	public function __construct( $context, $config, &$sktemplate, &$links ) {
		parent::__construct( $context, $config );

		$this->sktemplate = $sktemplate;
		$this->links = &$links;
	}
}