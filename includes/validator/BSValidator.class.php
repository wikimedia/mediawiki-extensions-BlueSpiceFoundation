<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * Call BsValidator::isValid() to validate several types of user input
 * (Email, username, URLs, ...)
 * Can be extended with plugin-classes of name: "BsValidator{$type}Plugin" that implement
 * interface BsValidatorPlugin
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

/* Changelog
 * v0.1
 * EMPTY YET
*/

// Last review MRG20100816

/*
 * == left2do ==
 *
 * Validators that check
 *     # Existencies (e.g. Namespaces, Groups, Usernames, ...)
 *     # Article-names
 *     # Telephone numbers
 *     # Dates
 *
 * FILTER_VALIDATE_INT
 * FILTER_VALIDATE_REGEXP
*/

/**
 * DEPRECATED!
 * @deprecated since version 3.1 - Use ParamProcessor instead
 */
class BsValidator {

	protected static $prKnownPlugins = [];

	/**
	 * DEPRECATED!
	 * Call BsValidator::isValid to validate several types of user input
	 * The return value is boolean per default but can be set to BsValidatorResponse via
	 * $opstions['fullResponse'] = true
	 * @deprecated since version 3.1 - Use ParamProcessor instead
	 * @param string $type of 'Email', 'Url', ...
	 * @param string $validateThis user input to be validated
	 * @param array $options is optional and may contain parameters for the validation-plugin
	 * @return mixed Boolean per default. A BsValidatorResponse-Object is
	 * returned IF ($options['fullResponse'] != false)
	 * <p>Examples:<br />
	 * - BsValidator::isValid(
	 *     'Url',
	 *     'http://mue.de',
	 *     [
	 *         'fullResponse' => true,
	 *         'flags' => FILTER_FLAG_PATH_REQUIRED|FILTER_FLAG_QUERY_REQUIRED
	 *     ]
	 *   );<br />
	 * - BsValidator::isValid('Email', 'so@domain.com');<br />
	 * - BsValidator::isValid('Ip', '0.0.0.0');<br />
	 * </p>
	 */
	public static function isValid( $type, $validateThis, $options = [] ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( !is_array( $options ) ) {
			throw new BsException( 'BsValidator::isValid called with 3rd param that is no array' );
		}
		$plugin = "BsValidator{$type}Plugin";
		if ( !class_exists( $plugin ) ) {
			throw new BsException( "BsValidatorPlugin of type: $plugin does not exist." );
		}
		// TODO MRG20100816: entweder $prKnownPlugins initialisieren oder hier auf is_array testen
		if ( !in_array( $type, self::$prKnownPlugins ) ) {
			$test = new ReflectionClass( $plugin );
			if ( !$test->implementsInterface( 'BsValidatorPlugin' ) ) {
				throw new BsException(
					"BsValidatorPlugin of type: $type does not implement 'BsValidatorPlugin'."
				);
			}
			self::$prKnownPlugins[] = $type;
		}

		$validationResult = call_user_func( $plugin . '::isValid', $validateThis, $options );

		if ( is_object( $validationResult ) && ( $validationResult instanceof BsValidatorResponse ) ) {
			return ( array_key_exists( 'fullResponse', $options ) && $options['fullResponse'] )
				? $validationResult
				// return boolean (success: 'true')
				: ( $validationResult->getErrorCode() === 0 );
		}
		throw new BsException( "$plugin did not return a BsValidatorResponse-object." );
	}

}
