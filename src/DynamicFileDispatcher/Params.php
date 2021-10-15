<?php

namespace BlueSpice\DynamicFileDispatcher;

class Params {
	public const MODULE = 'module';

	public const PARAM_TYPE = 'type';
	public const PARAM_DEFAULT = 'default';

	public const TYPE_STRING = 'string';
	public const TYPE_BOOL = 'boolean';
	public const TYPE_INT = 'integer';

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

		if ( !isset( $this->params[$name] ) ) {
			return $return;
		}

		switch ( $definition[static::PARAM_TYPE] ) {
			case static::TYPE_BOOL:
				$return = $this->params[$name] ? true : false;
				break;
			case static::TYPE_INT:
				$return = is_numeric( $this->params[$name] )
					? (int)$this->params[$name]
					: $definition[static::PARAM_DEFAULT];
				break;
			case static::TYPE_STRING:
				$return = is_string( $this->params[$name] )
					? $this->params[$name]
					: $definition[static::PARAM_DEFAULT];
				break;
		}
		return $return;
	}
}
