<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

/**
 * DEPRECATED!
 * Interface for plugin for BsValidator.
 * Single function is 'isValid' with parameters as follows:
 * $validateThis : the value to be checked against
 * $options : associative array.
 * The function HAS TO RETURN a BsValidatorResponse
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
interface BsValidatorPlugin {

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param mixed $validateThis the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $validateThis, $options );

}
