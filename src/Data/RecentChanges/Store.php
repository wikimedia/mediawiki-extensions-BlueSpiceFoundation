<?php

namespace BlueSpice\Data\RecentChanges;

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
	public function __construct( $context ) {
		$this->context = $context;
	}

	public function getReader() {
		return new Reader( \MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer(), $this->context );
	}

	public function getWriter() {
		throw new NoWriterException();
	}
}
