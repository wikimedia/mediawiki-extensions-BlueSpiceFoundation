<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * Call BsValidator::isValid() to validate several types of user input (Email, username, URLs, ...)
 * Can be extended with plugin-classes of name: "BsValidator{$type}Plugin" that implement interface BsValidatorPlugin
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Mathias Scheer
 * @version 0.1.0 beta
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
 * Interface for plugin for BsValidator.
 * Single function is 'isValid' with parameters as follows:
 * $validateThis : the value to be checked against
 * $options : associative array.
 * The function HAS TO RETURN a BsValidatorResponse
 */
interface BsValidatorPlugin {

	/**
	 * @param mixed $validateThis the value to be checked against
	 * @param array $options
	 * @return BsValidatorResponse If type is not BsValidatorResponse an error is thrown
	 */
	public static function isValid( $validateThis, $options );

}

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
		wfProfileIn( 'BS::'.__METHOD__ );
		if ( !is_array( $options ) ) {
			throw new BsException( 'BsValidator::isValid called with 3rd param that is no array' ); // todo: throw new BsException
		}
		$plugin = "BsValidator{$type}Plugin";
		// TODO MRG20100816: Sollte man hier nicht den Autoloader verwenden?
		if ( !class_exists( $plugin, false ) ) {
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
			wfProfileOut( 'BS::'.__METHOD__ );
			return ( array_key_exists( 'fullResponse', $options ) && $options['fullResponse'] )
				? $validationResult
				: ( $validationResult->getErrorCode() === 0 ); // return boolean (success: 'true')
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		throw new BsException( "$plugin did not return a BsValidatorResponse-object." ); // todo: throw new BsException
	}

}

class BsValidatorResponse {

	// TODO MRG20100816: Kurzer Kommentar, was der Zweck der Variablen ist.
	protected $mErrorcode;
	protected $mI18NInstanceName;
	protected $mI18NMessageKey;
	protected $mI18NTokens;
	protected $mI18NRenderedString = null;

	/**
	 * @param int $errorcode HAS TO BE (int) '0' if no error occurred
	 * @param string $sI18NInstanceName Name of BsI18N-Instance in Blue spice's mechanism for internationalization, where message translation can be found
	 * @param string $sI18NMessageKey key for BsI18N translation of error
	 * @param mixed $vI18NTokens spoken-word-params for BsI18N, can be single string or array with numeric keys
	 */
	public function __construct( $errorcode, $sI18NInstanceName = null, $sI18NMessageKey = null, $vI18NTokens = null ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$this->mErrorcode = $errorcode;
		$this->mI18NInstanceName = $sI18NInstanceName;
		$this->mI18NMessageKey = $sI18NMessageKey;
		$this->mI18NTokens = $vI18NTokens;
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	/**
	 * @return int Is 0 if no error occurred
	 */
	public function getErrorCode() {
		return $this->mErrorcode;
	}

	/**
	 * @return string Human readable errormessage in User's language
	 */
	public function getI18N() {
		wfProfileIn( 'BS::'.__METHOD__ );
		if ( is_null( $this->mI18NInstanceName ) ) {
			return false;
		}

		if ( is_null( $this->mI18NRenderedString ) ) {
			$this->mI18NRenderedString = ( is_null( $this->mI18NTokens ) )
				? wfMessage( $this->mI18NMessageKey )->text()
				// TODO MRG (08.02.11 00:08): msg wurde modifiziert, $default gibts nicht mehr. @Robert: macht Tokens von $default Gebrauch?
				: wfMessage( $this->mI18NMessageKey, $this->mI18NTokens )->text();
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return $this->mI18NRenderedString;

		// @todo @sebastian : hier wird dem BsI18N-Mechanismus ENTWEDER
		//     # ein array (z.B. array('wort1', 'wort2', ... , 'wort99') oder
		//     # ein einzelner String
		// übergeben
	}

}

class BsValidatorEmailPlugin implements BsValidatorPlugin {

	/*
	==RFC specification==
	E-mail addresses are formally defined in RFC
	5322 (mostly section 3.4.1) and to a lesser degree RFC 5321. An e-mail
	address is a string of a subset of [[ASCII]] characters (see however the
	internationalized addresses below) separated into 2 parts by an "@"
	([[at sign]]), a "local-part" and a domain, that is,
	<code>local-part@domain</code>.

	The local-part of an e-mail address may be up to 64 characters long and
	the [[domain name]] may have a maximum of 255 characters. However, the
	maximum length of a forward or reverse path length of 256 characters
	restricts the entire e-mail address to be no more than 254
	characters.<ref>RFC 5321,
	[http://tools.ietf.org/html/rfc5321#section-4.5.3.1 section 4.5.3.1].
	''Size Limits and Minimums'' explicitly details protocol limits.</ref>
	Some mail protocols, such as [[X.400]], may require larger objects,
	however. The SMTP specification recommends that software implementations
	impose no limits for the lengths of such objects.

	The local-part of the e-mail address may use any of these ASCII
	characters:

	* Uppercase and lowercase English letters (a–z, A–Z) * Digits
	<code>0</code> to <code>9</code> * Characters <code>! # $ % & ' * + - /
	= ? ^ _ ` { | } ~</code> * Character <code>.</code> (dot, period, full
	stop) provided that it is not the first or last character, and provided
	also that it does not appear two or more times consecutively (e.g.
	John..Doe@example.com).

	Additionally, quoted-strings (e.g.
	<code>"John&nbsp;Doe"@example.com</code>) are permitted, thus allowing
	characters that would otherwise be prohibited, however they do not
	appear in common practice. RFC 5321 also warns that "a host that expects
	to receive mail SHOULD avoid defining mailboxes where the Local-part
	requires (or uses) the Quoted-string form" (sic).

	The local-part is case sensitive, so "jsmith@example.com" and
	"JSmith@example.com" may be delivered to different people. This practice
	is discouraged by RFC 5321. However, only the authoritative mail servers
	for a domain may make that decision. The only exception is for a
	local-part value of "postmaster" which is case insensitive, and should
	be forwarded to the server's administrator.

	The domain name is much more restricted: it must match the requirements
	for a [[hostname]], consisting of letters, digits, hyphens and dots. In
	addition, the domain may be an [[IP address]] literal, surrounded by
	square braces, such as <code>jsmith@[192.168.2.1]</code>.

	The informational RFC 3696 written by the author of RFC 5321 explains
	the details in a readable way, with a few minor errors noted in the
	[http://www.rfc-editor.org/cgi-bin/errataSearch.pl?rfc=3696 3696
	errata].
	*/

	public static function isValid( $email, $options ) {
		$result = filter_var( $email, FILTER_VALIDATE_EMAIL ); // return is boolean
		return ( $result === false )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-email-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-email-validation-approved' );
	}
}

/**
 * called via BsValidatorPlugin::isValid('Url', 'http://example.com', $options)
 * $options can contain the key 'flags' that may hold a sum of the following flags:
 *     # FILTER_FLAG_PATH_REQUIRED
 *     # FILTER_FLAG_QUERY_REQUIRED
 */
class BsValidatorUrlPlugin implements BsValidatorPlugin
{

	public static function isValid( $validateThis, $options ) {
		$params = ( array_key_exists( 'flags', $options ) && $options['flags'] != 0 )
			? $options['flags']
			: null;

		// PHP bug workaround (http://bugs.php.net/51192)
		if ( !filter_var( 'http://www.blue-spice.org/', FILTER_VALIDATE_URL ) ) {
			$validateThis = str_replace( '-', '_', $validateThis );
		}

		$result = ( is_null( $params ) )
			? filter_var( $validateThis, FILTER_VALIDATE_URL )
			: filter_var( $validateThis, FILTER_VALIDATE_URL, $params ); // return is boolean

		return ( $result === false )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-url-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-url-validation-approved' );
	}
}

// TODO MRG20100816: Kommentar
class BsValidatorPositiveIntegerPlugin implements BsValidatorPlugin
{
	public static function isValid( $validateThis, $options ) {
		return ( !is_numeric( $validateThis) || $validateThis < 0 )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-positive-integer-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-positive-integer-validation-approved' );
	}
}

// TODO MRG20100816: Kommentar, auch Fehlercodes beschreiben
class BsValidatorIntegerRangePlugin implements BsValidatorPlugin
{
	public static function isValid( $validateThis, $options ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if ( is_numeric( $validateThis ) ) {
			if ( isset( $options['lowerBoundary'] ) && ( $validateThis < $options['lowerBoundary'] ) ) {
				$response = new BsValidatorResponse( 1, 'Validator', 'bs-validator-integer-range-validation-too-low', $options['lowerBoundary'] );
			} else if ( isset( $options['upperBoundary'] ) && ( $validateThis > $options['upperBoundary'] ) ) {
				$response = new BsValidatorResponse( 2, 'Validator', 'bs-validator-integer-range-validation-too-high', $options['upperBoundary'] );
			} else {
				$response = new BsValidatorResponse( 0, 'Validator', 'bs-validator-integer-range-validation-approved' );
			}
		} else {
			$response = new BsValidatorResponse( 3, 'Validator', 'bs-validator-integer-range-validation-no-integer' );
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return $response;
	}
}

// TODO MRG20100816: Ich habs zwar programmiert, aber wofür isses da?
class BsValidatorArgCountPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		return ( $validateThis < 0 )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-arg-count-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-arg-count-validation-approved' );
	}

}

// TODO MRG20100816: Kommentar
class BsValidatorCategoryPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		// TODO: find a better filter
		return ( !is_string( $validateThis ) )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-category-validation-not-approved' )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-category-validation-approved' );
	}
}

// TODO MRG20100816: Kommentar
class BsValidatorSetItemPlugin implements BsValidatorPlugin {

	public static function isValid( $validateThis, $options ) {
		return ( !in_array( $validateThis , $options['set'] ) )
			? new BsValidatorResponse( 1, 'Validator', 'bs-validator-set-validation-not-approved', array( $options['setname'], implode( ',', $options['set'] ) ) )
			: new BsValidatorResponse( 0, 'Validator', 'bs-validator-set-validation-approved', array( $options['setname']) );
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
