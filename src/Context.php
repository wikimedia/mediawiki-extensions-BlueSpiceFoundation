<?php
namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\Authority;
use MediaWiki\Session\CsrfTokenSet;

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

	/** @var MediaWikiServices */
	protected $services = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( IContextSource $context, Config $config ) {
		$this->context = $context;
		$this->config = $config;
		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 * @inheritDoc
	 */
	public function canUseWikiPage() {
		wfDeprecated( __METHOD__, '1.37' );
		return $this->context->getTitle()->canExist();
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
	public function getLanguageCode() {
		return $this->context->getLanguageCode();
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
		wfDeprecated( __METHOD__, '1.27' );
		return $this->services->getStatsdDataFactory();
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
		wfDeprecated( __METHOD__, '1.37' );
		return $this->services->getWikiPageFactory()->newFromTitle( $this->context->getTitle() );
	}

	/**
	 * @inheritDoc
	 */
	public function getActionName(): string {
		return $this->context->getActionName();
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

	/**
	 * @inheritDoc
	 */
	public function getCsrfTokenSet(): CsrfTokenSet {
		return new CsrfTokenSet( $this->getRequest() );
	}
}
