<?php

namespace BlueSpice;

use BlueSpice\Renderer\Params;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;

class RendererFactory {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IRegistry $registry
	 * @param Config $config
	 */
	public function __construct( $registry, $config ) {
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 *
	 * @param string $key
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @return Renderer
	 */
	public function get( $key, Params $params, ?IContextSource $context = null ) {
		$callable = $this->registry->getValue(
			$key,
			'\\BlueSpice\\Renderer\\NullRenderer::factory'
		);
		if ( !is_callable( $callable ) ) {
			// Deprecated since 3.1! All renderer should be registered with a factory
			// callback
			$instance = new $callable(
				$this->config,
				$params,
				$this->getServices()->getLinkRenderer(),
				$context,
				$key
			);
			wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
			return $instance;
		}

		return call_user_func_array( $callable, [
			$key,
			$this->getServices(),
			$this->config,
			$params,
			$context
		] );
	}

	/**
	 *
	 * @return MediaWikiServices
	 */
	public function getServices() {
		return MediaWikiServices::getInstance();
	}

}
