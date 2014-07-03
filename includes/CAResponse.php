<?php

class BsCAIResponse extends AjaxResponse {

	protected $mPayload = null;
	protected $bSuccess = true;
	protected $sMessage = '';

	/**
	 *
	 * @param string $sPermission
	 * @param BsCAContext $oCAContext
	 * @return BsCAResponse
	 */
	public static function newFromPermission( $sPermission, $oCAContext = null ) {
		$bUserCan = true;
		if( $oCAContext instanceof BsCAContext ) {
			$bUserCan = $oCAContext->getTitle()->userCan( $sPermission );
		}
		else {
			$bUserCan = RequestContext::getMain()->getTitle()->userCan( $sPermission );
		}

		$bSuccess = true;
		$sMessage = '';

		if( $bUserCan == false ) {
			$bSuccess = false;
			$sMessage = 'permissionserrors';
		}

		$oResponse = new self();
		$oResponse->setSuccess( $bSuccess );
		$oResponse->setMessage( $sMessage );

		return $oResponse;
	}

	public function __construct( $bSuccess = true, $mPayload = '', $sMessage = '' ) {
		parent::__construct( null );

		//HINT: http://www.ietf.org/rfc/rfc4627.txt
		$this->mContentType = 'application/json';

		$this->bSuccess = $bSuccess;
		$this->mPayload = $mPayload;
		$this->sMessage = $sMessage;
	}

	public function setPayload( $mPayload ) {
		$this->mPayload = $mPayload;
	}

	public function setSuccess( $bSuccess ) {
		if( $bSuccess ) {
			$this->setResponseCode( '200 OK' );
		}
		else {
			$this->setResponseCode( '403 Forbidden' );
		}
		$this->bSuccess = $bSuccess;
	}

	public function isSuccess() {
		return $this->bSuccess;
	}

	public function setMessage( $sKeyOrMessage, $bNoKey = false ) {
		if( $bNoKey || empty($bNoKey) ) {
			$this->sMessage = $sKeyOrMessage;
		}
		else {
			$this->sMessage = wfMessage( $sKeyOrMessage )->plain();
		}
	}

	public function printText() {
		$this->addText( FormatJson::encode(
				array(
					'success' => $this->bSuccess,
					'message' => $this->sMessage,
					'payload' => $this->mPayload,
				)
			)
		);
		parent::printText();
	}
}

class BsCAResponse extends BsCAIResponse {}