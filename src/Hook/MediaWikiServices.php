<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class MediaWikiServices extends Hook {
	/**
	 *
	 * @var \MediaWiki\MediaWikiServices
	 */
	protected $services;

	/**
	 *
	 * @param \MediaWiki\MediaWikiServices $services
	 * @return boolean
	 */
	public static function callback( $services ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$services
		);
		return $hookHandler->process();
	}

	public function __construct( $context, $config, $services ) {
		parent::__construct( $context, $config );

		$this->services = $services;
	}
}
