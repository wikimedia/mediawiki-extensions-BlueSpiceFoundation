<?php

namespace BlueSpice\Api\Task;

use BlueSpice\Api\Response\Standard;
use MediaWiki\Api\ApiBase;
use MediaWiki\Status\Status;

class StatusConverter {

	/**
	 *
	 * @var Status
	 */
	protected $status = null;

	/**
	 *
	 * @var ApiBase
	 */
	protected $api = null;

	/**
	 *
	 * @param ApiBase $api
	 * @param Status $status
	 */
	public function __construct( $api, $status ) {
		$this->api = $api;
		$this->status = $status;
	}

	/**
	 *
	 */
	public function convert() {
		$res = new Standard();
		if ( !$this->status->isOK() ) {
			$res->{Standard::ERRORS} = $this->api->getErrorFormatter()->arrayFromStatus(
				$this->status
			);
			// $res->{Standard::MESSAGE} = $this->status->getMessage();
		} else {
			$res->{Standard::SUCCESS} = true;
			$res->{Standard::PAYLOAD} = $this->status->getValue();
			$res->{Standard::PAYLOAD_COUNT} = count( $this->status->getValue() );
		}
		foreach ( (array)$res as $name => $field ) {
			if ( $name === Standard::ERRORS && empty( $field ) ) {
				continue;
			}
			$this->api->getResult()->addValue( null, $name, $field );
		}
	}
}
