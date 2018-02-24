<?php

namespace BlueSpice\ParamProcessor;

/**
 * Translates hardcoded error messages from "DataValues" and "ParamProcessor"
 * See https://github.com/JeroenDeDauw/ParamProcessor/issues/31
 */
class ProcessingErrorMessageTranslator {

	/**
	 * @var array
	 */
	protected $messagePatterns = [
		//https://github.com/search?q=org%3ADataValues+throw+new+ParseException&type=Code
		'#The value is not recognitzed by the configured parsers#' => 'bs-validator-value-not-recognized',
		'#Not a boolean#' => 'bs-validator-invalid-boolean',
		'#Not a float#' => 'bs-validator-invalid-float',
		'#Not an integer#' => 'bs-validator-invalid-integer',
		'#Not a string#' => 'bs-validator-invalid-string',
		'#Unable to explode coordinate segment by degree symbol \((.*?)\)#' => 'bs-validator-invalid-coordinate',
		'#Did not find degree symbol \((.*?)\)#' => 'bs-validator-invalid-coordinate',
		'#Unable to split input into two coordinate segments#' => 'bs-validator-invalid-coordinate',
		'#The format of the coordinate could not be determined.#' => 'bs-validator-invalid-coordinate',
		'#The format of the coordinate could not be determined. Parsing failed.#' => 'bs-validator-invalid-coordinate',
		'#Not a valid geographical coordinate#' => 'bs-validator-invalid-coordinate',
		'#(.*?): Unable to split string (.*?) into two coordinate segments#' => 'bs-validator-invalid-coordinate',

		//https://github.com/JeroenDeDauw/ParamProcessor/search?l=PHP&q=registerNewError&type=&utf8=%E2%9C%93
		"#(.*?) is not a valid parameter#" => 'bs-validator-invalid-parameter',
		"#Required parameter '(.*?)' is missing#" => 'bs-validator-missing-required'
	];

	/**
	 *
	 * @param string $message
	 * @return string
	 */
	public function translate( $message ) {
		foreach( $this->messagePatterns as $pattern => $i18nKey ) {
			if( preg_match( $pattern, $message ) !== 0 ) {
				return wfMessage( $i18nKey )->plain();
			}
		}
		return $message;
	}
}
