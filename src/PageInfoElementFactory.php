<?php

namespace BlueSpice;

use Config;
use IContextSource;
use MWException;

class PageInfoElementFactory {

	/**
	 *
	 * @var PageInfoElement[]
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
	 * @return PageInfoElement[]
	 */
	public function getAll( IContextSource $context = null ) {
		$pageInfoElements = [];
		foreach ( $this->registry->getAllKeys() as $key ) {
			$instance = $this->get( $key, $context );
			$pageInfoElements[$key] = $instance;
		}

		usort( $pageInfoElements, static function ( $a, $b ) {
			return $a->getPosition() > $b->getPosition();
		} );

		return $pageInfoElements;
	}

	/**
	 *
	 * @param string $key
	 * @param IContextSource|null $context
	 * @return IPageInfoElement
	 * @throws MWException
	 */
	public function get( $key, IContextSource $context = null ) {
		if ( !$context ) {
			$context = $this->context;
		}
		if ( isset( $this->instances[$this->instanceKey( $key, $context )] ) ) {
			return $this->instances[$this->instanceKey( $key, $context )];
		}
		$callback = $this->registry->getValue( $key );
		if ( !is_callable( $callback ) ) {
			throw new MWException(
				"Callback for info element '$key' not callable"
			);
		}
		$instance = call_user_func_array(
			$callback,
			[ $this->context, $this->config ]
		);
		if ( $instance instanceof IPageInfoElement === false ) {
			throw new MWException(
				"Class for info element '$key' does not extend IPageInfoElement"
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
				"No Title to create instance key for IPageInfoElement '$key'"
			);
		}
		$text = $context->getTitle()->getText();

		return "$key-$text";
	}
}
