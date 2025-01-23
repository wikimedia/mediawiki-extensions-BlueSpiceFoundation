<?php

namespace BlueSpice\Data\Categorylinks;

use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\IStore;
use MWStake\MediaWiki\Component\DataStore\NoWriterException;

class Store implements IStore {

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var bool
	 */
	protected $filterForContextUser = false;

	/**
	 *
	 * @param IContextSource $context
	 * @param bool $filterForContextUser
	 */
	public function __construct( $context, $filterForContextUser = false ) {
		$this->context = $context;
		$this->filterForContextUser = $filterForContextUser;
	}

	/**
	 *
	 * @return Reader
	 */
	public function getReader() {
		return new Reader(
			MediaWikiServices::getInstance()->getDBLoadBalancer(),
			$this->context,
			null,
			$this->filterForContextUser
		);
	}

	/**
	 *
	 * @throws NoWriterException
	 */
	public function getWriter() {
		throw new NoWriterException();
	}
}
