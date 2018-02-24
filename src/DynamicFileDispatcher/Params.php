<?php

namespace BlueSpice\DynamicFileDispatcher;

class Params {
	const MODULE = 'module';

	const PARAM_TYPE = 'type';
	const PARAM_DEFAULT = 'default';

	const TYPE_STRING = 'string';
	const TYPE_BOOL = 'boolean';
	const TYPE_INT = 'integer';

	/**
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 *
	 * @param array $params
	 */
	public function __construct( array $params = [] ) {
		$this->params = $params;
	}

	/**
	 * @param string $name
	 * @param array $definition
	 * @return mixed
	 */
	public function get( $name, $definition ) {
		$return = $definition[static::PARAM_DEFAULT];

		if( !isset( $this->params[$name] ) ) {
			return $return;
		}

		switch ( $definition[static::PARAM_TYPE] ) {
			case static::TYPE_BOOL:
				$return = $this->params[$name] ? true : false;
				break;
			case static::TYPE_INT:
				$return = is_numeric( $this->params[$name] )
					? (int) $this->params[$name]
					: $definition[static::PARAM_DEFAULT]
				;
				break;
			case static::TYPE_STRING:
				$return = is_string( $this->params[$name] )
					? $this->params[$name]
					: $definition[static::PARAM_DEFAULT]
				;
				break;
		}
		return $return;
	}
}
