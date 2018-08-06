<?php

namespace BlueSpice\DynamicFileDispatcher;

class UrlBuilder {
	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @var Factory
	 */
	protected $dfdFactory = null;

	protected $dynamicFilePath = "/dynamic_file.php";

	/**
	 *
	 * @param Factory $wfdFactory
	 * @param \Config $config
	 */
	public function __construct( Factory $wfdFactory, $config ) {
		$this->dfdFactory = $wfdFactory;
		$this->config = $config;
	}

	/**
	 * Returns the relative url to a dynamic file
	 * @param Params $params
	 * @param \IContextSource|null $context
	 * @return string
	 */
	public function build( Params $params, \IContextSource $context = null ) {
		$dfd = $this->dfdFactory->newFromParams( $params, $context, false );

		$urlArr = [
			'module' => $dfd->getModuleName(),
		];

		return $this->config->get( 'ScriptPath' )
			."$this->dynamicFilePath?"
			.wfArrayToCgi( $urlArr, $dfd->getParams() );
	}
}
