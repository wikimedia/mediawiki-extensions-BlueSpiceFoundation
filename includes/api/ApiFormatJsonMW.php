<?php

class ApiFormatJson extends ApiFormatBase {

	private $mIsRaw;

	public function __construct( $main, $format ) {
		parent::__construct( $main, $format );
		$this->mIsRaw = ( $format === 'rawfm' );
	}

	public function getMimeType() {
		$params = $this->extractRequestParams();
		// callback:
		if ( $params['callback'] ) {
			return 'text/javascript';
		}
		return 'text/html';
	}

	public function getNeedsRawData() {
		return $this->mIsRaw;
	}

	public function getWantsHelp() {
		// Help is always ugly in JSON
		return false;
	}

	public function execute() {
		$prefix = $suffix = '';

		$params = $this->extractRequestParams();
		$callback = $params['callback'];
		if ( !is_null( $callback ) ) {
			$prefix = preg_replace( "/[^][.\\'\\\"_A-Za-z0-9]/", '', $callback ) . '(';
			$suffix = ')';
		}
 
		$this->printText(
			$prefix .
			FormatJson::encode( $this->getResultData(), $this->getIsHtml() ) .
			$suffix
		);
	}

	public function getAllowedParams() {
		return array(
			'callback'  => null,
		);
	}

	public function getParamDescription() {
		return array(
			'callback' => 'If specified, wraps the output into a given function call. For safety, all user-specific data will be restricted.',
		);
	}

	public function getDescription() {
		if ( $this->mIsRaw ) {
			return 'Output data with the debuging elements in JSON format' . parent::getDescription();
		} else {
			return 'Output data in JSON format' . parent::getDescription();
		}
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiFormatJson.php 70647 2010-08-07 19:59:42Z ialex $';
	}
}
