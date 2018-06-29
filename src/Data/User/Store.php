<?php

namespace BlueSpice\Data\User;

use BlueSpice\Data\NoWriterException;

class Store implements \BlueSpice\Data\IStore {

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @param \IContextSource $context
	 */
	public function __construct( $context, $loadBalancer ) {
		$this->context = $context;
		$this->loadBalancer = $loadBalancer;
	}

	public function getReader() {
		return new Reader( $this->loadBalancer, $this->context );
	}

	public function getWriter() {
		throw new NoWriterException();
	}
}
