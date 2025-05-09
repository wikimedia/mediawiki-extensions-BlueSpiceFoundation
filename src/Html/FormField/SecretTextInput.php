<?php

namespace BlueSpice\Html\FormField;

use MediaWiki\HTMLForm\Field\HTMLTextField;
use OOUI\Widget;

class SecretTextInput extends HTMLTextField {

	/** @var string */
	private $secretValue;

	/**
	 * @param array $params
	 */
	public function __construct( $params ) {
		$params['type'] = 'password';
		parent::__construct( $params );
		$this->secretValue = $params['secretValue'] ?? '';
	}

	/**
	 * @stable to override
	 *
	 * @param array $params
	 *
	 * @return Widget
	 */
	protected function getInputWidget( $params ) {
		$params['value'] = $this->secretValue;
		return new \OOUI\TextInputWidget( $params );
	}
}
