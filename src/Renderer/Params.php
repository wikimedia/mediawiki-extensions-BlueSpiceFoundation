<?php

namespace BlueSpice\Renderer;

class Params {

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
	 * @param array $default
	 * @return mixed
	 */
	public function get( $name, $default ) {
		if( !isset( $this->params[$name] ) ) {
			return $default;
		}
		return $this->params[$name];
	}
}
