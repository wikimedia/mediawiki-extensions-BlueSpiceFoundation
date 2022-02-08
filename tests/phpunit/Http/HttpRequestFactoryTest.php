<?php

namespace BlueSpice\Tests\Http;

use BlueSpice\Http\HttpRequestFactory;
use MediaWiki\Http\HttpRequestFactory as MWHttpRequestFactory;
use PHPUnit\Framework\TestCase;

class HttpRequestFactoryTest extends TestCase {

	/**
	 * @covers BlueSpice\Http\HttpRequestFactory::canMakeRequests
	 * @covers BlueSpice\Http\HttpRequestFactory::create
	 * @covers BlueSpice\Http\HttpRequestFactory::createMultiClient
	 * @covers BlueSpice\Http\HttpRequestFactory::get
	 * @covers BlueSpice\Http\HttpRequestFactory::getUserAgent
	 * @covers BlueSpice\Http\HttpRequestFactory::post
	 * @covers BlueSpice\Http\HttpRequestFactory::request
	 * @return void
	 */
	public function testDefaultOptions() {
		$defaultOptions = [
			'sslVerifyHost' => false,
			'sslVerifyCert' => true
		];
		$mockFactory = $this->createMock( MWHttpRequestFactory::class );
		$mockFactory->expects( $this->atLeastOnce() )->method( 'canMakeRequests' );
		$mockFactory->expects( $this->atLeastOnce() )->method( 'create' )->with(
			$this->equalTo( 'https://www.bluespice.com' ),
			$this->equalTo( $defaultOptions )
		);
		$mockFactory->expects( $this->atLeastOnce() )->method( 'createMultiClient' )->with( $this->equalTo( [
			'sslVerifyHost' => false,
			'sslVerifyCert' => false
		] ) );
		$mockFactory->expects( $this->atLeastOnce() )->method( 'get' )->with(
			$this->equalTo( 'https://www.bluespice.com' ),
			$this->equalTo( $defaultOptions )
		);
		$mockFactory->expects( $this->atLeastOnce() )->method( 'getUserAgent' );
		$mockFactory->expects( $this->atLeastOnce() )->method( 'post' )->with(
			$this->equalTo( 'https://www.bluespice.com' ),
			$this->equalTo( $defaultOptions )
		);
		$mockFactory->expects( $this->atLeastOnce() )->method( 'request' )->with(
			$this->equalTo( 'PUT' ),
			$this->equalTo( 'https://www.bluespice.com' ),
			$this->equalTo( $defaultOptions )
		);

		$decoratorFactory = new HttpRequestFactory( $mockFactory, $defaultOptions );

		$decoratorFactory->canMakeRequests();
		$decoratorFactory->create( 'https://www.bluespice.com' );
		$decoratorFactory->createMultiClient( [
			'sslVerifyCert' => false
		] );
		$decoratorFactory->get( 'https://www.bluespice.com' );
		$decoratorFactory->getUserAgent();
		$decoratorFactory->post( 'https://www.bluespice.com' );
		$decoratorFactory->request( 'PUT', 'https://www.bluespice.com' );
	}

}
