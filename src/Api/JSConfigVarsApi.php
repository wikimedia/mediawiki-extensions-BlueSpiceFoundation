<?php

namespace BlueSpice\Api;

use BlueSpice\Api;
use BlueSpice\JSConfigVarRegistry;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;

class JSConfigVarsApi extends Api {
	public const FUNC_GET = 'get';
	public const FUNC_HAS = 'has';

	/** @var string|null */
	protected $func = null;
	/** @var array|null */
	protected $requestedVars = null;
	/** @var array */
	protected $context = [];

	/**
	 * @inheritDoc
	 */
	public function execute() {
		try {
			$this->readInParams();
			$value = $this->retrieveValues();
			$this->returnResult( $value );
		} catch ( MWException $ex ) {
			$this->dieWithError( $ex->getMessage() );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getAllowedParams() {
		return parent::getAllowedParams() + [
			'func' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => 'string',
				static::PARAM_HELP_MSG => 'apihelp-bs-js-var-config-param-func',
			],
			'name' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
				static::PARAM_HELP_MSG => 'apihelp-bs-js-var-config-param-name',
			],
			'context' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '',
				static::PARAM_HELP_MSG => 'apihelp-bs-js-var-config-param-context',
			],
			'format' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_DEFAULT => '',
				static::PARAM_HELP_MSG => 'apihelp-bs-js-var-config-param-format',
			],
		];
	}

	/**
	 * @throws MWException
	 */
	private function readInParams() {
		$this->func = trim( strtolower( $this->getParameter( 'func' ) ) );
		if ( !in_array( $this->func, [ static::FUNC_GET, static::FUNC_HAS ] ) ) {
			throw new MWException( "Function '{$this->func}' is not allowed!" );
		}
		$this->requestedVars = $this->parseRequestedVars(
			$this->getParameter( 'name' )
		);
	}

	/**
	 * @return array|mixed|null
	 * @throws MWException
	 */
	private function retrieveValues() {
		$registry = JSConfigVarRegistry::factory( $this->getContext() );

		if ( empty( $this->requestedVars ) ) {
			return null;
		}

		return $this->getValues( $registry );
	}

	/**
	 * @param mixed $value
	 */
	private function returnResult( $value ) {
		$result = $this->getResult();
		$result->addValue( null, 'success', 1 );
		$result->addValue( null, 'payload', $value );
	}

	/**
	 * @param string $raw
	 * @return array
	 */
	private function parseRequestedVars( $raw ) {
		$vars = explode( '|', $raw );
		return array_map( static function ( $item ) {
			return trim( $item );
		}, $vars );
	}

	/**
	 * @param string $key
	 * @param JSConfigVarRegistry $registry
	 * @return mixed
	 * @throws MWException
	 */
	private function getSingle( $key, $registry ) {
		if ( $this->func === static::FUNC_HAS ) {
			return $registry->hasValue( $key );
		}
		if ( $this->func === static::FUNC_GET && $registry->hasValue( $key ) ) {
			return $registry->getValue( $key, null );
		}

		return null;
	}

	/**
	 * @param JSConfigVarRegistry $registry
	 * @return array
	 * @throws MWException
	 */
	private function getValues( $registry ) {
		$values = [];
		foreach ( $this->requestedVars as $var ) {
			$values[$var] = $this->getSingle( $var, $registry );
		}

		return $values;
	}
}
