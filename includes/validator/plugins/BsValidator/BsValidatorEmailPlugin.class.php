<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Mathias Scheer
 */

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
