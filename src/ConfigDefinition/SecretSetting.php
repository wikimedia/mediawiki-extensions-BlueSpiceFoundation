<?php

namespace BlueSpice\ConfigDefinition;

use BlueSpice\Html\FormField\SecretTextInput;
use MediaWiki\HTMLForm\HTMLFormField;

abstract class SecretSetting extends StringSetting {

	public const SECRET_VALUE = '##secret##';

	/**
	 *
	 * @return HTMLFormField
	 */
	public function getHtmlFormField() {
		return new SecretTextInput( $this->makeFormFieldParams() );
	}

	/**
	 * @return array
	 */
	protected function makeFormFieldParams() {
		$params = parent::makeFormFieldParams();
		$params['secretValue'] = $this->makeSecretValue();
		return $params;
	}

	/**
	 * @return string
	 */
	public function makeSecretValue(): string {
		$actual = $this->getValue();
		if ( !$actual ) {
			return '';
		}
		return static::SECRET_VALUE . $this->padToLength( strlen( $actual ) );
	}

	/**
	 * @param string $totalLen
	 * @return string
	 */
	public function padToLength( string $totalLen ): string {
		$length = strlen( static::SECRET_VALUE );
		return str_repeat( '*', $totalLen - $length );
	}
}
