<?php
namespace BlueSpice;

class Context implements \IContextSource {

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

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( \IContextSource $context, \Config $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 * Check whether a WikiPage object can be get with getWikiPage().
	 * Callers should expect that an exception is thrown from getWikiPage()
	 * if this method returns false.
	 *
	 * @since 1.19
	 * @return bool
	 */
	public function canUseWikiPage() {
		return $this->context->canUseWikiPage();
	}

	/**
	 * Export the resolved user IP, HTTP headers, user ID, and session ID.
	 * The result will be reasonably sized to allow for serialization.
	 *
	 * @return array
	 * @since 1.21
	 */
	public function exportSession() {
		return $this->context->exportSession();
	}

	/**
	 * Get the site configuration
	 *
	 * @since 1.23
	 * @return \Config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Get the Language object
	 *
	 * @return Language
	 * @since 1.19
	 */
	public function getLanguage() {
		return $this->context->getLanguage();
	}

	/**
	 * Get the OutputPage object
	 *
	 * @return OutputPage
	 */
	public function getOutput() {
		return $this->context->getOutput();
	}

	/**
	 * Get the WebRequest object
	 *
	 * @return WebRequest
	 */
	public function getRequest() {
		return $this->context->getRequest();
	}

	/**
	 * Get the Skin object
	 *
	 * @return Skin
	 */
	public function getSkin() {
		return $this->context->getSkin();
	}

	/**
	 * Get the Stats object
	 *
	 * @deprecated since 1.27 use a StatsdDataFactory from MediaWikiServices (preferably injected)
	 *
	 * @since 1.25
	 * @return IBufferingStatsdDataFactory
	 */
	public function getStats() {
		return $this->context->getStats();
	}

	/**
	 * Get the Timing object
	 *
	 * @since 1.27
	 * @return Timing
	 */
	public function getTiming() {
		return $this->context->getTiming();
	}

	/**
	 * Get the Title object
	 *
	 * @return Title|null
	 */
	public function getTitle() {
		return $this->context->getTitle();
	}

	/**
	 * Get the User object
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->context->getUser();
	}

	/**
	 * Get the WikiPage object.
	 * May throw an exception if there's no Title object set or the Title object
	 * belongs to a special namespace that doesn't have WikiPage, so use first
	 * canUseWikiPage() to check whether this method can be called safely.
	 *
	 * @since 1.19
	 * @return WikiPage
	 */
	public function getWikiPage() {
		return $this->context->getWikiPage();
	}

	/**
	 * This is the method for getting translated interface messages.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Messages_API
	 * @see Message::__construct
	 *
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed $params,... Normal message parameters
	 * @return Message
	 */
	public function msg( $key ) {
		return $this->context->msg( $key );
	}

}
