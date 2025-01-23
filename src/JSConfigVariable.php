<?php

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class JSConfigVariable implements IJSConfigVariable {
	/** @var IContextSource */
	protected $context = [];
	/** @var Config|null */
	protected $config = null;

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @return static
	 */
	public static function factory( IContextSource $context, Config $config ) {
		return new static ( $context, $config );
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( IContextSource $context, Config $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 * @return Config|null
	 */
	protected function getConfig() {
		return $this->config;
	}

	/**
	 * @return IContextSource
	 */
	protected function getContext() {
		return $this->context;
	}
}
