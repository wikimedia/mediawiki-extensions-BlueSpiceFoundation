<?php

namespace BlueSpice\Hook;

abstract class LoadExtensionSchemaUpdates extends Hook {
	
	/**
	 *
	 * @var \DatabaseUpdater
	 */
	protected $updater = null;

	/**
	 *
	 * @param \DatabaseUpdater $updater
	 * @return boolean
	 */
	public static function callback( $updater ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$updater
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \DatabaseUpdater $updater
	 */
	public function __construct( $context, $config, $updater ) {
		parent::__construct( $context, $config );

		$this->updater = $updater;
	}
}