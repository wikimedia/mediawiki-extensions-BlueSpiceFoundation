<?php

namespace BlueSpice\DynamicFileDispatcher;

class RequestParams extends Params {

	/**
	 *
	 * @var \WebRequest
	 */
	protected $request = null;

	/**
	 *
	 * @param array $params
	 * @param \WebRequest|null $request
	 */
	public function __construct( array $params = [], \WebRequest $request = null ) {
		parent::__construct( $params );
		$this->request = $request;
		if( !$this->request ) {
			$this->request = \RequestContext::getMain()->getRequest();
		}
	}

	/**
	 * @param string $name
	 * @param array $definition
	 * @return mixed
	 */
	public function get( $name, $definition ) {
		//Given params overwrite request params
		if( isset( $this->params[$name] ) ) {
			return parent::get( $name, $definition );
		}
		switch( $definition[static::PARAM_TYPE] ) {
			case static::TYPE_BOOL:
				$methodName = 'getBool';
				break;
			case static::TYPE_INT:
				$methodName = 'getInt';
				break;
			case static::TYPE_STRING:
			default:
				$methodName = 'getVal';
		}

		return $this->request->$methodName(
			$name,
			$definition[static::PARAM_DEFAULT]
		);
	}
}
