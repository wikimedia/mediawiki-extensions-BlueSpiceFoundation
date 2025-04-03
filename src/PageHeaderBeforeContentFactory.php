<?php

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MWException;

class PageHeaderBeforeContentFactory {

	/**
	 *
	 * @var PageHeaderBeforeContentElement[]
	 */
	private $instances = [];

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	private $registry = null;

	/**
	 *
	 * @var IContextSource
	 */
	private $context = null;

	/**
	 *
	 * @var Config
	 */
	private $config = null;

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( ExtensionAttributeBasedRegistry $registry,
		IContextSource $context, Config $config ) {
		$this->registry = $registry;
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 * @param IContextSource|null $context
	 * @return PageHeaderBeforeContentElement[]
	 */
	public function getAll( ?IContextSource $context = null ) {
		$elements = [];
		foreach ( $this->registry->getAllKeys() as $key ) {
			$instance = $this->get( $key, $context );
			$elements[$key] = $instance;
		}

		usort( $elements, static function ( $a, $b ) {
			return $a->getPosition() > $b->getPosition();
		} );

		return $elements;
	}

	/**
	 *
	 * @param string $key
	 * @param IContextSource|null $context
	 * @return IPageHeaderBeforeContent
	 * @throws MWException
	 */
	public function get( $key, ?IContextSource $context = null ) {
		if ( !$context ) {
			$context = $this->context;
		}
		if ( isset( $this->instances[$this->instanceKey( $key, $context )] ) ) {
			return $this->instances[$this->instanceKey( $key, $context )];
		}
		$callback = $this->registry->getValue( $key );
		if ( !is_callable( $callback ) ) {
			throw new MWException(
				"Callback for element '$key' not callable"
			);
		}
		$instance = call_user_func_array(
			$callback,
			[ $this->context, $this->config ]
		);
		if ( $instance instanceof IPageHeaderBeforeContent === false ) {
			throw new MWException(
				"Class for info element '$key' does not extend IPageHeaderBeforeContent"
			);
		}
		$this->instances[$this->instanceKey( $key, $context )] = $instance;
		return $instance;
	}

	/**
	 *
	 * @param type $key
	 * @param IContextSource $context
	 * @return string
	 */
	private function instanceKey( $key, IContextSource $context ) {
		if ( !$context->getTitle() ) {
			throw new MWException(
				"No Title to create instance key for IPageHeaderBeforeContent '$key'"
			);
		}
		$text = $context->getTitle()->getText();

		return "$key-$text";
	}
}
