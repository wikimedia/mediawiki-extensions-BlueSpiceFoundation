<?php
/**
 * Internationalisation file for BlueSpice
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    BlueSpice_Core
 * @subpackage BlueSpice
 * @copyright  Copyright (C) 2012 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

$messages = array();

$messages['en'] = array(
	'bs-ns_main' => '(Pages)',
	'bs-ns_all' => '(all)',
	'bs-tab_navigation' => 'Navigation',
	'bs-tab_focus' => 'Focus',
	'bs-tab_admin' => 'Admin',
	'bs-userbar_loginswitch_logout' => 'Log out',
	'bs-userbar_loginswitch_login' => 'Log in',
	'bs-extended-search-tooltip-fulltext' => 'Fulltext search',
	'bs-extended-search-textfield-defaultvalue' => 'Search...',
	'bs-extended-search-textfield-tooltip' => 'Search wiki',
	'bs-no-information-available' => 'No information available',
	'bs-years-duration' => '{{PLURAL:$1|one year|$1 years}}',
	'bs-months-duration' => '{{PLURAL:$1|one month|$1 months}}',
	'bs-weeks-duration' => '{{PLURAL:$1|one week|$1 weeks}}',
	'bs-days-duration' => '{{PLURAL:$1|one day|$1 days}}',
	'bs-hours-duration' => '{{PLURAL:$1|one hour|$1 hours}}',
	'bs-mins-duration' => '{{PLURAL:$1|one minute|$1 minutes}}',
	'bs-secs-duration' => '{{PLURAL:$1|one second|$1 seconds}}',
	'bs-two-units-ago' => '$1 and $2 ago',
	'bs-one-unit-ago' => '$1 ago',
	'bs-now' => 'now',
	'bs-email-greeting-receiver' => "{{GENDER:$1|Hello Mr $1|Hello Mrs $1|Hello $1}},",
	'bs-email-greeting-no-receiver' => "Hello,",
	'bs-email-footer' => "This message was generated automatically. Please do not reply to this email.",
	'bs-userpagesettings-legend' => 'User settings',
	'bs-userpreferences-link-text' => 'More user settings',
	'bs-userpreferences-link-title' => 'Display your personal user settings',
	'bs-exception-view-heading' => 'An error occured',
	'bs-exception-view-text' => 'While processing your request the following error occured:',
	'bs-exception-view-admin-hint' => 'Please contact your system administrator.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Show error details',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Hide error details',
	'action-responsibleeditors-viewspecialpage' => 'view pages which are protected with the "ResponsibleEditors-Viewspecialpage" right',
	'bs-viewtagerrorlist-legend' => '$1 - Error',
	'bs-readonly' => 'The database is currently locked to new entries and other modifications, probably for routine database maintenance, after which it will be back to normal. The administrator who locked it offered this explanation: $1',
	'bs-imageofotheruser' => 'You are not allowed to upload an image for another user.',
	'bs-pref-sortalph' => 'Sort namespaces alphabetically',
	'bs-permissionerror' => 'Permission error!',
	'bs-filesystemhelper-no-directory' => '<code>$1</code> is not a valid directory.',
	'bs-filesystemhelper-has-path-traversal' => 'Path traversal detected!',
	'bs-filesystemhelper-file-not-exists' => 'The file <code>$1</code> does not exist.',
	'bs-filesystemhelper-file-copy-error' => 'The file <code>$1</code> could not be copied.',
	'bs-filesystemhelper-file-already-exists' => 'The file <code>$1</code> already exists.',
	'bs-filesystemhelper-file-delete-error' => 'The file <code>$1</code> could not be deleted.',
	'bs-filesystemhelper-folder-already-exists' => 'The folder <code>$1</code> already exists.',
	'bs-filesystemhelper-folder-copy-error' => 'The folder <code>$1</code> could not be renamed.',
	'bs-filesystemhelper-folder-not-exists' => 'The folder <code>$1</code> does not exist.',
	'bs-filesystemhelper-upload-err-code' => 'The file could not be uploaded: $1',
	'bs-filesystemhelper-upload-wrong-ext' => 'The file does not have the required extension: $1.',
	'bs-filesystemhelper-upload-unsupported-type' => 'This file type is not supported.',
	'bs-filesystemhelper-save-base64-error' => 'The file could not be saved.',
	'bs-filesystemhelper-upload-base64-error' => 'The file could not be uploaded.',
	'bs-filesystemhelper-upload-local-error-stash-file'=>'The file could not be moved to the upload stash.',
	'bs-filesystemhelper-upload-local-error-create' => 'The file could not be created in the wiki.',
	'bs-navigation-instructions' => 'Instructions',
	'bs-navigation-support' => 'Support',
	'bs-navigation-contact' => 'Contact'
);

$messages['qqq'] = array(
	'bs-ns_main' => 'Label used in namespace dropdown lists for NS_MAIN',
	'bs-ns_all' => 'Label used in namespace dropdown lists for all namespaces',
	'bs-tab_navigation' => 'Label for "nagivation" tab in left sidebar',
	'bs-tab_focus' => 'Label for "focus" tab in left sidebar',
	'bs-tab_admin' => 'Label for "admin" tab in left sidebar',
	'bs-userbar_loginswitch_logout' => 'Label for logout button in personal toolbar',
	'bs-userbar_loginswitch_login' => 'Label for login button in personal toolbar',
	'bs-extended-search-tooltip-fulltext' => 'Tooltip shown on mouse over the button at the corner of the search field on every page when in fulltext mode',
	'bs-extended-search-textfield-defaultvalue' => 'Default text of the search field shown on every page',
	'bs-extended-search-textfield-tooltip' => 'Tootip shown on mouse over the search field on every page',
	'bs-no-information-available' => 'Default text shown in widgets when there is no content',
	'bs-years-duration' => 'Text for more than one year in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-months-duration' => 'Text for more than one month in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-weeks-duration' => 'Text for more than one week in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-days-duration' => 'Text for more than one day in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-hours-duration' => 'Text for more than one hour in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-mins-duration' => 'Text for more than one minute in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-secs-duration' => 'Text for more than one second in a duration. Used in {{msg-mw|bs-one-unit-ago}} and {{msg-mw|bs-two-units-ago}}.

Parameters:
* $1 - a positive number',
	'bs-two-units-ago' => 'Text shown when indicating how long ago an event was. Uses {{msg-mw|bs-years-duration}}, {{msg-mw|bs-months-duration}}, {{msg-mw|bs-days-duration}}, {{msg-mw|bs-hours-duration}}, {{msg-mw|bs-minutes-duration}}, {{msg-mw|bs-seconds-duration}} for parameters

Parameters:
* $1 - a duration
* $2 - a duration one unit smaller than $1',

	'bs-one-unit-ago' => 'Text shown when indicating how long ago an event was

Parameters:
* $1 - a duration',
	'bs-now' => 'Text shown for "now" when indicating how long ago an event was',
	'bs-email-greeting-receiver' => "Used in plain text mails as first line in body to greet the reveiver, $1 is the current user name for GENDER distinction (depends on sex setting)

Parameters:
* $1 - name of the receiver",
	'bs-email-greeting-no-receiver' => "Used in plain text mails as first line in body to greet the reveiver",
	'bs-email-footer' => "Used in plain text mails as last line",
	'bs-userpagesettings-legend' => 'Label for section with links to special user related settings',
	'bs-userpreferences-link-text' => 'Label for link to user preferences on user page',
	'bs-userpreferences-link-title' => 'Title (shown as flyout) for link to user preferences on user page',
	'bs-exception-view-heading' => 'Heading for error message',
	'bs-exception-view-text' => 'First line of an error message',
	'bs-exception-view-admin-hint' => 'Text in error message with additional hints, e.g. contact the administrator',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Label for toggler that shows the complete stack trace (normally hidden)',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Label for toggler that hides the complete stack trace (when shown)',
	'bs-viewtagerrorlist-legend' => 'Used in inline error messages produced by tags

Parameters:
* $1 - name of the tag',
	'bs-readonly' => 'Shown in various messages when database is in readonly mode and a write operation is requested.

Parameters:
* $1 - explanation as given in $wgReadOnly',
	'bs-imageofotheruser' => 'Error message when user tries to upload an avatar for another user',
	'bs-pref-sortalph' => 'Label for preferences item to sort all namespaces alphabetically',
	'bs-permissionerror' => 'Error message sent in ajax requests when user has no permissions to perform that action',
	'bs-filesystemhelper-no-directory' => 'Error message given when requested directory is not a directory, put $1 in code tags

Parameters:
* $1 - name of the requested directory',
	'bs-filesystemhelper-has-path-traversal' => 'Error message given when a file path tries to access files outside mediawiki root',
	'bs-filesystemhelper-file-not-exists' => 'Error message given when a file does not exist, put $1 in code tags

Parameters:
* $1 - name of the file',
	'bs-filesystemhelper-file-copy-error' => 'Error message given when a file could not be copied, put $1 in code tags

Parameters:
* $1 - name of the file',
	'bs-filesystemhelper-file-already-exists' => 'Error message given when a file already exists, put $1 in code tags

Parameters:
* $1 - name of the file',
	'bs-filesystemhelper-file-delete-error' => 'Error message given when a file could not be deleted, put $1 in code tags

Parameters:
* $1 - name of the file',
	'bs-filesystemhelper-folder-already-exists' => 'Error message given when a folder already exists, put $1 in code tags

Parameters:
* $1 - name of the folder',
	'bs-filesystemhelper-folder-copy-error' => 'Error message given when a folder could not be copied, put $1 in code tags

Parameters:
* $1 - name of the folder',
	'bs-filesystemhelper-folder-not-exists' => 'Error message given when a folder does not exists, put $1 in code tags

Parameters:
* $1 - name of the folder',
	'bs-filesystemhelper-upload-err-code' => 'Error message given when a file could not be uploaded, full stop included in $1

Parameters:
* $1 - a string indicating the reason',
	'bs-filesystemhelper-upload-wrong-ext' => 'Error message given when an uploaded file does not have the required file extension

Parameters:
* $1 - the required file extension',
	'bs-filesystemhelper-upload-unsupported-type' => 'Error message given when an uploaded file has a file type that is not supported',
	'bs-navigation-instructions' => 'Used in navigation for instuctions link',
	'bs-navigation-support' => 'Used in navigation for support link',
	'bs-navigation-contact' => 'Used in navigation for contact link'
);

$messages['de'] = array(
	'bs-ns_main' => '(Seiten)',
	'bs-ns_all' => '(alle)',
	'bs-tab_navigation' => 'Navigation',
	'bs-tab_focus' => 'Fokus',
	'bs-tab_admin' => 'Admin',
	'bs-userbar_loginswitch_logout' => 'Abmelden',
	'bs-userbar_loginswitch_login' => 'Anmelden',
	'bs-extended-search-tooltip-fulltext' => 'Volltextsuche',
	'bs-extended-search-textfield-defaultvalue' => 'Suche...',
	'bs-extended-search-textfield-tooltip' => 'Wiki durchsuchen',
	'bs-no-information-available' => 'Keine Informationen verfügbar',
	'bs-years-duration' => '{{PLURAL:$1|einem Jahr|$1 Jahren}}',
	'bs-months-duration' => '{{PLURAL:$1|einem Monat|$1 Monaten}}',
	'bs-weeks-duration' => '{{PLURAL:$1|einer Woche|$1 Wochen}}',
	'bs-days-duration' => '{{PLURAL:$1|einem Tag|$1 Tagen}}',
	'bs-hours-duration' => '{{PLURAL:$1|einer Stunde|$1 Stunden}}',
	'bs-mins-duration' => '{{PLURAL:$1|einer Minute|$1 Minuten}}',
	'bs-secs-duration' => '{{PLURAL:$1|einer Sekunde|$1 Sekunden}}',
	'bs-two-units-ago' => 'vor $1 und $2',
	'bs-one-unit-ago' => 'vor $1',
	'bs-now' => 'jetzt',
	'bs-email-greeting-receiver' => "{{GENDER:$1|Hallo Herr $1|Hallo Frau $1|Hallo $1}},",
	'bs-email-greeting-no-receiver' => "Hallo,",
	'bs-email-footer' => "Dies ist eine automatisch generierte E-Mail. Bitte antworte nicht auf diese E-Mail!",
	'bs-userpagesettings-legend' => 'Benutzereinstellungen',
	'bs-userpreferences-link-text' => 'Weitere Benutzereinstellungen',
	'bs-userpreferences-link-title' => 'Zeigt deine persönlichen Benutzereinstellungen an',
	'bs-exception-view-heading' => 'Es ist ein Fehler aufgetreten',
	'bs-exception-view-text' => 'Bei der Verarbeitung Deiner Anfrage ist folgender Fehler aufgetreten:',
	'bs-exception-view-admin-hint' => 'Bitte kontaktiere Deinen Administrator.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Fehlerdetails anzeigen',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Fehlerdetails verbergen',
	'bs-viewtagerrorlist-legend' => '$1 - Fehler',
	'bs-readonly' => 'Die Datenbank ist vorübergehend für Neueinträge und Änderungen gesperrt. Bitte versuche es später noch einmal. Grund der Sperrung: $1. ',
	'bs-imageofotheruser' => 'Du bist nicht berechtigt ein Bild für einen anderen Benutzer hochzuladen.',
	'bs-pref-sortalph' => 'Namensräume alphabetisch sortieren',
	'bs-permissionerror' => 'Berechtigungsfehler!',
	'bs-filesystemhelper-no-directory' => '<code>$1</code> ist kein gültiges Verzeichnis.',
	'bs-filesystemhelper-has-path-traversal' => 'Path traversal entdeckt!',
	'bs-filesystemhelper-file-not-exists' => 'Die Datei <code>$1</code> existiert nicht.',
	'bs-filesystemhelper-file-copy-error' => 'Die Datei <code>$1</code> konnte nicht kopiert werden.',
	'bs-filesystemhelper-file-already-exists' => 'Die Datei <code>$1</code> existiert bereits.',
	'bs-filesystemhelper-file-delete-error' => 'Die Datei <code>$1</code> konnte nicht gelöscht werden.',
	'bs-filesystemhelper-folder-already-exists' => 'Der Ordner <code>$1</code> existiert bereits.',
	'bs-filesystemhelper-folder-copy-error' => 'Der Ordner <code>$1</code> konnte nicht umbenannt werden.',
	'bs-filesystemhelper-folder-not-exists' => 'Der Ordner <code>$1</code> existiert nicht.',
	'bs-filesystemhelper-upload-err-code' => 'Die Datei konnte nicht hochgeladen werden: $1',
	'bs-filesystemhelper-upload-wrong-ext' => 'Die Datei hat nicht das richtige Dateiformat: $1',
	'bs-filesystemhelper-upload-unsupported-type' => 'Dieser Dateityp wird nicht unterstützt.',
	'bs-filesystemhelper-save-base64-error' => 'Die Datei konnte nicht gespeichert werden.',
	'bs-filesystemhelper-upload-base64-error' => 'Die Datei konnte nicht hochgeladen werden.',
	'bs-filesystemhelper-upload-local-error-stash-file'=>'Die Datei konnte nicht in den Uploadstash verschoben werden.',
	'bs-filesystemhelper-upload-local-error-create' => 'Die Datei konnte im Wiki nicht angelegt werden.',
	'bs-navigation-instructions' => 'Anleitung',
	'bs-navigation-support' => 'Support',
	'bs-navigation-contact' => 'Kontakt'
);

$messages['de-formal'] = array(
	'bs-email-footer' => "Dies ist eine automatisch generierte E-Mail. Bitte antworten Sie nicht auf diese E-Mail!",
	'bs-userpreferences-link-title' => 'Zeigt Ihre persönlichen Benutzereinstellungen an.',
	'bs-exception-view-admin-hint' => 'Bitte kontaktieren Sie Ihren Administrator.',
	'bs-imageofotheruser' => 'Sie sind nicht berechtigt, ein Bild für einen anderen Benutzer hochzuladen.'
);
