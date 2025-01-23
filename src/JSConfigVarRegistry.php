<?php

namespace BlueSpice;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MWException;

class JSConfigVarRegistry extends ExtensionAttributeBasedRegistry {

	/** @var IContextSource */
	protected $context;

	/**
	 * @param IContextSource $context
	 * @return static
	 */
	public static function factory( IContextSource $context ) {
		return new static( $context );
	}

	/**
	 * @inheritDoc
	 */
	public function __construct( IContextSource $context ) {
		parent::__construct( 'BlueSpiceFoundationJSConfigVars' );

		$this->context = $context;
	}

	/**
	 * Disabled, does not make sense in this application
	 *
	 * @return array
	 */
	public function getAllValues() {
		return [];
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getValue( $key, $default = '' ) {
		$value = parent::getValue( $key, null );
		$instance = $this->createFromCallback( $value, $this->context );

		if ( !$instance instanceof IJSConfigVariable ) {
			throw new MWException(
				get_class( $instance ) . " must return an instance of " . IJSConfigVariable::class
			);
		}

		return $instance->getValue();
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function hasValue( $key ) {
		return in_array( $key, $this->getAllKeys() );
	}

	/**
	 * @param string $callback
	 * @param IContextSource $context
	 * @return IJSConfigVariable|null
	 */
	private function createFromCallback( $callback, IContextSource $context ) {
		if ( !is_callable( $callback ) ) {
			return null;
		}

		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		return call_user_func_array( $callback, [ $context, $config ] );
	}
}
