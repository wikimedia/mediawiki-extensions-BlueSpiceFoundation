<?php
namespace BlueSpice;

use Config;
use IContextSource;
use MediaWiki\Permissions\Authority;

class Context implements IContextSource {

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
	public function __construct( IContextSource $context, Config $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function canUseWikiPage() {
		return $this->context->canUseWikiPage();
	}

	/**
	 * @inheritDoc
	 */
	public function exportSession() {
		return $this->context->exportSession();
	}

	/**
	 * @inheritDoc
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @inheritDoc
	 */
	public function getLanguage() {
		return $this->context->getLanguage();
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput() {
		return $this->context->getOutput();
	}

	/**
	 * @inheritDoc
	 */
	public function getRequest() {
		return $this->context->getRequest();
	}

	/**
	 * @inheritDoc
	 */
	public function getSkin() {
		return $this->context->getSkin();
	}

	/**
	 * @inheritDoc
	 */
	public function getStats() {
		return $this->context->getStats();
	}

	/**
	 * @inheritDoc
	 */
	public function getTiming() {
		return $this->context->getTiming();
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->context->getTitle();
	}

	/**
	 * @inheritDoc
	 */
	public function getUser() {
		return $this->context->getUser();
	}

	/**
	 * @inheritDoc
	 */
	public function getWikiPage() {
		return $this->context->getWikiPage();
	}

	/**
	 * @inheritDoc
	 */
	public function msg( $key, ...$params ) {
		return $this->context->msg( $key, ...$params );
	}

	/**
	 * @return Authority
	 */
	public function getAuthority(): Authority {
		return $this->context->getAuthority();
	}

}
