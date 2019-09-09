<?php

namespace BlueSpice\Hook;

use MediaWiki\MediaWikiServices as Services;
use IContextSource;
use Config;
use BlueSpice\Hook;

abstract class MediaWikiServices extends Hook {
	/**
	 *
	 * @var Services
	 */
	protected $services;

	/**
	 *
	 * @param Services $services
	 * @return bool
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

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Services $services
	 */
	public function __construct( $context, $config, $services ) {
		parent::__construct( $context, $config );

		$this->services = $services;
	}
}
