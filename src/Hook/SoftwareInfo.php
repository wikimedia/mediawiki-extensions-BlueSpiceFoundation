<?php

namespace BlueSpice\Hook;

abstract class SoftwareInfo extends Hook {

	protected $softwareInfo = [];

	/**
	 * Called by Special:Version for returning information about the software
	 * @param array $software
	 * @return boolean
	 */
	public static function callback( &$software ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$software
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param array $software
	 */
	public function __construct( $context, $config, &$software ) {
		parent::__construct( $context, $config );

		$this->softwareInfo =& $software;
	}
}