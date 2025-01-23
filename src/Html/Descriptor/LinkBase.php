<?php

namespace BlueSpice\Html\Descriptor;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class LinkBase implements ILink {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( $context, $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlId() {
		return '';
	}

	/**
	 *
	 * @return string[]
	 */
	public function getCSSClasses() {
		return [];
	}

	/**
	 *
	 * @return string
	 */
	public function getIcon() {
		return '';
	}

	/**
	 *
	 * @return array
	 */
	public function getDataAttributes() {
		return [];
	}

	/**
	 *
	 * @param \ContextSource $context
	 * @param Config $config
	 * @return ILink[]
	 */
	public static function factory( $context, $config ) {
		return [ __CLASS__ => new static( $context, $config ) ];
	}
}
