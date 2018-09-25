<?php

namespace BlueSpice\PageTool;

use BlueSpice\IPageTool;

abstract class Base implements IPageTool {

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	public function __construct( $context, $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @return IPageTool
	 */
	public static function factory( $context, $config ) {
		return new static( $context, $config );
	}

	public function getHtml() {
		if( $this->skipProcessing() ) {
			return '';
		}

		return $this->doGetHtml();
	}

	/**
	 *
	 * @return string[]
	 */
	public function getPermissions() {
		return [ 'read' ];
	}

	/**
	 *
	 * @return int
	 */
	public function getPosition() {
		return 100;
	}

	/**
	 * Convenience method
	 * @return \User
	 */
	protected function getUser() {
		return $this->context->getUser();
	}

	/**
	 * Convenience method
	 * @return \Title
	 */
	protected function getTitle() {
		return $this->context->getTitle();
	}

	/**
	 * @return string
	 */
	protected abstract function doGetHtml();

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		// By default page tools are only available fpr content pages. Subclasses may also allow
		// other page types like "SpecialPage"
		return $this->getTitle()->isContentPage() === false;
	}
}
