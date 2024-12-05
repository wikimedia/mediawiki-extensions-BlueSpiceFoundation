<?php

namespace BlueSpice\Http;

use MediaWiki\Http\HttpRequestFactory as MWHttpRequestFactory;

class HttpRequestFactory extends MWHttpRequestFactory {

	/**
	 *
	 * @var MWHttpRequestFactory
	 */
	private $decoratedFactory = null;

	/**
	 *
	 * @var array
	 */
	private $defaultOptions = [];

	/**
	 *
	 * @param MWHttpRequestFactory $decoratedFactory
	 * @param array $defaultOptions
	 */
	public function __construct( $decoratedFactory, $defaultOptions = [] ) {
		$this->decoratedFactory = $decoratedFactory;
		$this->defaultOptions = $defaultOptions;
	}

	/**
	 * @param array $options
	 * @return array
	 */
	private function amendOptions( $options ) {
		$mergedOptions = array_merge( $this->defaultOptions, $options );
		return $mergedOptions;
	}

	/**
	 * @inheritDoc
	 */
	public function create( $url, array $options = [], $caller = __METHOD__ ) {
		$options = $this->amendOptions( $options );
		return $this->decoratedFactory->create( $url, $options, $caller );
	}

	/**
	 * @inheritDoc
	 */
	public function canMakeRequests() {
		return $this->decoratedFactory->canMakeRequests();
	}

	/**
	 * @inheritDoc
	 */
	public function request( $method, $url, array $options = [], $caller = __METHOD__ ) {
		$options = $this->amendOptions( $options );
		return $this->decoratedFactory->request( $method, $url, $options, $caller );
	}

	/**
	 * @inheritDoc
	 */
	public function get( $url, array $options = [], $caller = __METHOD__ ) {
		$options = $this->amendOptions( $options );
		return $this->decoratedFactory->get( $url, $options, $caller );
	}

	/**
	 * @inheritDoc
	 */
	public function post( $url, array $options = [], $caller = __METHOD__ ) {
		$options = $this->amendOptions( $options );
		return $this->decoratedFactory->post( $url, $options, $caller );
	}

	/**
	 * @inheritDoc
	 */
	public function getUserAgent() {
		return $this->decoratedFactory->getUserAgent();
	}

	/**
	 * @inheritDoc
	 */
	public function createMultiClient( $options = [] ) {
		$options = $this->amendOptions( $options );
		return $this->decoratedFactory->createMultiClient( $options );
	}

	/**
	 * @inheritDoc
	 */
	public function createGuzzleClient( array $config = [] ): \GuzzleHttp\Client {
		return $this->decoratedFactory->createGuzzleClient( $config );
	}
}
