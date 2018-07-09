<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * Call BsValidator::isValid() to validate several types of user input (Email, username, URLs, ...)
 * Can be extended with plugin-classes of name: "BsValidator{$type}Plugin" that implement interface BsValidatorPlugin
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

class BsValidator {

	protected static $prKnownPlugins = array();

	/**
	 * Call BsValidator::isValid to validate several types of user input
	 * The return value is boolean per default but can be set to BsValidatorResponse via $opstions['fullResponse'] = true
	 * @param string $type of 'Email', 'Url', ...
	 * @param string $validateThis user input to be validated
	 * @param array $options is optional and may contain parameters for the validation-plugin
	 * @return mixed Boolean per default. A BsValidatorResponse-Object is returned IF ($options['fullResponse'] != false)
	 * <p>Examples:<br />
	 * - BsValidator::isValid('Url', 'http://mue.de', array('fullResponse' => true, 'flags' => FILTER_FLAG_PATH_REQUIRED|FILTER_FLAG_QUERY_REQUIRED));<br />
	 * - BsValidator::isValid('Email', 'so@domain.com');<br />
	 * - BsValidator::isValid('Ip', '0.0.0.0');<br />
	 * </p>
	 */
	public static function isValid( $type, $validateThis, $options = array() ) {
		if ( !is_array( $options ) ) {
			throw new BsException( 'BsValidator::isValid called with 3rd param that is no array' ); // todo: throw new BsException
		}
		$plugin = "BsValidator{$type}Plugin";
		if ( !class_exists( $plugin ) ) {
			throw new BsException( "BsValidatorPlugin of type: $plugin does not exist." );
		}
		// TODO MRG20100816: entweder $prKnownPlugins initialisieren oder hier auf is_array testen
		if ( !in_array( $type, self::$prKnownPlugins ) ) {
			$test = new ReflectionClass( $plugin );
			if ( !$test->implementsInterface( 'BsValidatorPlugin' ) )
				throw new BsException( "BsValidatorPlugin of type: $type does not implement 'BsValidatorPlugin'." );
			self::$prKnownPlugins[] = $type;
		}

		$validationResult = call_user_func( $plugin.'::isValid', $validateThis, $options );

		if ( is_object( $validationResult ) && ( $validationResult instanceof BsValidatorResponse ) ) {
			return ( array_key_exists( 'fullResponse', $options ) && $options['fullResponse'] )
				? $validationResult
				: ( $validationResult->getErrorCode() === 0 ); // return boolean (success: 'true')
		}
		throw new BsException( "$plugin did not return a BsValidatorResponse-object." ); // todo: throw new BsException
	}

}

// ------------------------ END OF FILE JUST FOR TESTING -----------------------
/*
function EmailValidationTests()
{
	echo "\nTests that should be positive\n";

	$test = array(
		'me@0.0.0.0',
		'jsmith@[192.168.2.1]',
		'markus@glaser.de',
		'"Mathias" <mathias@example.com>',
		'<mathias@example.com>',
		' mathias@example.com ',
		' mathias@example.com',
		'mathias@example.com ',
	);

	foreach ($test as $addr)
	{
		$result = BsValidator::isValid('Email', $addr, array('fullResponse' => true));
		echo (($result->getErrorCode() != 0) ? "!" : " ")." = $addr \n";
	}

	echo "\nTest that should be negative\n";

	$test = array(
		'me@0.0.0.0.255',
		'me@500.500.500.500',
		'localhost',
		'Abc.example.com', // (character @ is missing)
		'Abc.@example.com', // (character dot(.) is last in local part)
		'Abc..123@example.com', // (character dot(.) is double)
		'A@b@c@example.com', // (only one @ is allowed outside quotations marks)
		'(mathias)@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'[mathias]@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'\mathias@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'ma;thias@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'ma:thias@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'ma,thias@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
		'<mathias>@example.com', // (none of the characters before the @ in this example are allowed outside quotation marks)
	);

	foreach ($test as $addr)
	{
		$result = BsValidator::isValid('Email', $addr, array('fullResponse' => true));
		echo (($result->getErrorCode() == 0) ? "!" : " ")." = $addr \n";
	}

	echo "Email-Tests durchgelaufen.\n";
}

function UrlValidationTests($options)
{

	echo "\nUrls that should pass BsValidatorUrlPlugin:\n";

	$test = array(
		'http://de.wikipedia.org',
		'http://de.wikipedia.org/path',
		'http://de.wikipedia.org?a=b',
		'http://de.wikipedia.org/path?a=b',
		'http://de.wikipedia.org/path?a=b&c=d',
		'ftp://hallowiki.biz',
		'scheme://username:password@domain:port/path?query_string#anchor',
	);

	foreach ($test as $addr)
	{
		$result = BsValidator::isValid('Url', $addr, $options);
		echo (($result->getErrorCode() != 0) ? "!" : " ")." = $addr \n";
	}

	echo "\nUrls not to pass BsValidatorUrlPlugin:\n";

	$test = array(
		'me@http://de.wikipedia.org',
		'scheme:username://password@domain:port/path?query_string#anchor',
	);

	foreach ($test as $addr)
	{
		$result = BsValidator::isValid('Url', $addr, $options);
		echo (($result->getErrorCode() == 0) ? "!" : " ")." = $addr \n";
	}

	echo "Url-Tests durchgelaufen.\n";

}

function IpValidationTests($options)
{

	echo "\nIPs\n";

	$test = array(
		'0.0.0.0',
		'255.255.255.255',
		'http://de.wikipedia.org?a=b',
		'192.168.1.1',
		'256.2.2.2',
		'1.2.3.4.5',
		'1.2.3',
	);

	foreach ($test as $addr)
	{
		$result = BsValidator::isValid('Ip', $addr, $options);
		echo (($result->getErrorCode() != 0) ? "!" : " ")." = $addr \n";
	}

	echo "IP-Tests durchgelaufen.\n";

}

function GroupnameValidationTests()
{
	echo "\nGroupname Tests:\n";

	$test = array(
		'Gruppe',
		'Gruppe\\',
		' Gruppe',
		'GruppeGruppeGruppe',
		'!"§$%&/()=??',
		null,
		' ',
		'',
	);

	foreach ($test as $group)
	{
		$result = BsValidator::isValid('MwGroupname', $group, array('fullResponse' => true));
		echo (($result->getErrorCode() != 0) ? "!" : " ")." = '$group' : ".$result->getBsI18N()."\n";
		//echo ((!$result) ? "!" : " ")." = '$group' : ".$result."\n";
	}

	echo "Groupname-Tests durchgelaufen.\n";
}

function NamespaceValidationTests()
{
	echo "\nNamespace Tests:\n";

	$test = array(
		'NameSp',
		'NameSp\\',
		' NameSp',
		'NameSpNameSpNameSp',
		'!"§$%&/()=??',
		null,
		' ',
		'',
	);

	foreach ($test as $namespace)
	{
		$result = BsValidator::isValid('MwNamespace', $namespace, array('fullResponse' => true));
		echo (($result->getErrorCode() != 0) ? "!" : " ")." = '$namespace' : ".$result->getBsI18N()."\n";
		//echo ((!$result) ? "!" : " ")." = '$namespace' : ".$result."\n";
	}

	echo "Namespace-Tests durchgelaufen.\n";
}

$d = BsValidator::isValid('MwUsername', 'Markus', array('fullResponse' => true));
var_dump($d);

UrlValidationTests(array('fullResponse' => true, 'flags' => FILTER_FLAG_PATH_REQUIRED|FILTER_FLAG_QUERY_REQUIRED));

IpValidationTests(array('fullResponse' => true));

EmailValidationTests();

GroupnameValidationTests();

NamespaceValidationTests();

*/
