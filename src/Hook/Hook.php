<?php
namespace BlueSpice\Hook;

abstract class Hook {

	/**
	 *
	 * @var \IContextSource
	 */
	private $context = null;

	/**
	 *
	 * @var \Config
	 */
	private $config = null;


	/**
	 * Normally both parameters are NULL on instantiation. This is because we
	 * perform a lazy loading out of performance reasons. But for the sake of
	 * testablity we keep the DI here
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( $context, $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @return \IContextSource
	 */
	protected function getContext() {
		if( $this->context instanceof \IContextSource === false ) {
			$this->context = \RequestContext::getMain();
		}
		return $this->context;
	}

	/**
	 *
	 * @var string
	 */
	protected static $configName = 'main';

	/**
	 *
	 * @return \Config
	 */
	protected function getConfig() {
		if( $this->config instanceof \Config === false ) {
			$this->config = \MediaWiki\MediaWikiServices::getInstance()
				->getConfigFactory()->makeConfig( static::$configName );
		}

		return $this->config;
	}

	public function process() {
		\Profiler::instance()->scopedProfileIn( "Hook ". __METHOD__ );
		$result = $this->doProcess();
		return $result;
	}

	protected abstract function doProcess();
}