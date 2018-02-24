<?php

namespace BlueSpice\Data\Entity;

abstract class Writer implements \BlueSpice\Data\IWriter {

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	public function __construct( \IContextSource $context = null, \Config $config = null ) {
		$this->context = $context;
		if( $this->context === null ) {
			$this->context = \RequestContext::getMain();
		}
		$this->config = $config;
		if( $this->config === null ) {
			$this->config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
		}
	}

	public function getSchema() {
		return new Schema();
	}

	/**
	 *
	 * @param array $dataSet
	 * @return \Status
	 */
	public function write( $dataSet ) {
		throw new Exception( 'Writing entity store is not supported yet' );
	}
}
