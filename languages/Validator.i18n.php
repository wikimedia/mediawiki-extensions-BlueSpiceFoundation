<?php
/**
 * Internationalisation file for Validator
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    BlueSpice_Core
 * @subpackage Validator
 * @copyright  Copyright (C) 2012 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

$messages = array();

$messages['en'] = array(
	'bs-validator-email-validation-approved' => 'The email address has been successfully validated.',
	'bs-validator-email-validation-not-approved' => 'The email address could not be validated.',
	'bs-validator-url-validation-approved' => 'The URL was successfully validated.',
	'bs-validator-url-validation-not-approved' => 'The URL could not be validated.',
	'bs-validator-arg-count-validation-approved' => 'The attribute "count" has been successfully validated.',
	'bs-validator-arg-count-validation-not-approved' => 'The attribute "count" could not be validated.',
	'bs-validator-set-validation-approved' => '$1 was successfully validated.',
	'bs-validator-set-validation-not-approved' => '{{PLURAL:$3|Only the following value is allowed for $1|For $1 only one of the following values is allowed}}: $2.',
	'bs-validator-integer-range-validation-too-low' => 'The provided value is too low. It should not be less than $1.',
	'bs-validator-integer-range-validation-too-high' => 'The provided value is too high. It should not be greater than $1.',
);

$messages['qqq'] = array(
	'bs-validator-email-validation-approved' => 'Used for confirmation message in dialogs and tags if the email address given is syntactically correct',
	'bs-validator-email-validation-not-approved' => 'Used for error message in dialogs and tags if the email address given contains syntax errors',
	'bs-validator-url-validation-approved' => 'Used for confirmation message in dialogs and tags if the URL given is syntactically correct',
	'bs-validator-url-validation-not-approved' => 'Used for error message in dialogs and tags if the URL given contains syntax errors',
	'bs-validator-arg-count-validation-approved' => 'Used for confirmation message in dialogs and tags if the tag attribute "count" is syntactically correct',
	'bs-validator-arg-count-validation-not-approved' => 'Used for error message in dialogs and tags if the tag attribute "count" contains syntax errors',
	'bs-validator-set-validation-approved' => 'Used for confirmation message in dialogs and tags if the value given is found in a set of options.

Parameters:
* $1 - name of the set of options',
	'bs-validator-set-validation-not-approved' => 'Used for error message in dialogs and tags if the value given is not found in a set of options.

Parameters:
* $1 - name of the set of options
* $2 - list of values that are in the set of options
* $3 - count of values that are in the set of options, use for PLURAL',
	'bs-validator-integer-range-validation-too-low' => 'Used for error message in dialogs and tags if the value given is below a certain integer value.

Parameters:
* $1 - lower boundary of the value range (included).',

	'bs-validator-integer-range-validation-too-high' => 'Used for error message in dialogs and tags if the value given is above a certain integer value.

Parameters:
* $1 - upper boundary of the value range (included).',
);

$messages['de'] = array(
	'bs-validator-email-validation-approved' => 'Die Email-Adresse wurde erfolgreich validiert.',
	'bs-validator-email-validation-not-approved' => 'Die Email-Adresse konnte nicht validiert werden.',
	'bs-validator-url-validation-approved' => 'Die URL wurde erfolgreich validiert.',
	'bs-validator-url-validation-not-approved' => 'Die URL konnte nicht validiert werden.',
	'bs-validator-arg-count-validation-approved' => 'Das Attribut "count" wurde erfolgreich validiert.',
	'bs-validator-arg-count-validation-not-approved' => 'Das Attribut "count" muss einen positiven Wert haben.',
	'bs-validator-set-validation-approved' => '$1 wurde erfolgreich validiert.',
	'bs-validator-set-validation-not-approved' => 'Für $1 ist nur {{PLURAL:$3|der folgende Wert|einer der folgenden Werte}} erlaubt: $2.',
	'bs-validator-integer-range-validation-too-low' => 'Der angegebene Wert ist zu klein. Er darf nicht kleiner als $1 sein.',
	'bs-validator-integer-range-validation-too-high' => 'Der angegebene Wert ist zu groß. Er darf nicht größer als $1 sein.',
);
