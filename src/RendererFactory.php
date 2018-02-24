<?php

namespace BlueSpice;
use BlueSpice\Renderer\Params;

class RendererFactory {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IRegistry $registry
	 * @param \Config $config
	 */
	public function __construct( $registry, $config ) {
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 *
	 * @param string $key
	 * @param Params $params
	 * @return IRenderer
	 */
	public function get( $key, Params $params ) {
		$className = $this->registry->getValue(
			$key,
			'\\BlueSpice\\Renderer\\NullRenderer'
		);
		$instance = new $className( $this->config, $params );
		return $instance;
	}
}
