<?php
// TODO RBV (08.04.11 10:24): So umbauen, dass es dem ExtJS Standard entspricht! { success: true/false }
abstract class BsXHRBaseResponse {
	public $status       = BsXHRResponseStatus::SUCCESS;
	public $shortMessage = '';
	public $longMessage  = '';

	public abstract function __toString();
}

class BsXHRJSONResponse extends BsXHRBaseResponse {
	public function __toString() {
		return json_encode( $this );
	}
}

class BsXHRResponseStatus {
	const SUCCESS = 'success';
	const ERROR   = 'error';
}