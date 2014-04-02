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

/** Message documentation (Message documentation)
 * @author Robby
 * @author Shirayuki
 */
$messages['qqq'] = array(
	'bs-ns_main' => 'Label used in namespace dropdown lists for NS_MAIN',
	'bs-ns_all' => 'Label used in namespace dropdown lists for all namespaces.
{{Identical|All}}',
	'bs-tab_navigation' => 'Label for "nagivation" tab in left sidebar',
	'bs-tab_focus' => 'Label for "focus" tab in left sidebar',
	'bs-tab_admin' => 'Label for "admin" tab in left sidebar',
	'bs-userbar_loginswitch_logout' => 'Label for logout button in personal toolbar.
{{Identical|Log out}}',
	'bs-userbar_loginswitch_login' => 'Label for login button in personal toolbar.
{{Identical|Log in}}',
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
	'bs-email-greeting-receiver' => 'Used in plain text mails as first line in body to greet the reveiver, $1 is the current user name for GENDER distinction (depends on sex setting)

Parameters:
* $1 - name of the receiver',
	'bs-email-greeting-no-receiver' => 'Used in plain text mails as first line in body to greet the reveiver',
	'bs-email-footer' => 'Used in plain text mails as last line',
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
	'bs-navigation-contact' => 'Used in navigation for contact link
{{Identical|Contact}}',
);

/** German (Deutsch)
 * @author Metalhead64
 */
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
	'bs-email-greeting-receiver' => '{{GENDER:$1|Hallo Herr $1|Hallo Frau $1|Hallo $1}},',
	'bs-email-greeting-no-receiver' => 'Hallo,',
	'bs-email-footer' => 'Dies ist eine automatisch generierte E-Mail. Bitte antworte nicht auf diese E-Mail!',
	'bs-userpagesettings-legend' => 'Benutzereinstellungen',
	'bs-userpreferences-link-text' => 'Weitere Benutzereinstellungen',
	'bs-userpreferences-link-title' => 'Zeigt deine persönlichen Benutzereinstellungen an',
	'bs-exception-view-heading' => 'Es ist ein Fehler aufgetreten',
	'bs-exception-view-text' => 'Bei der Verarbeitung Deiner Anfrage ist folgender Fehler aufgetreten:',
	'bs-exception-view-admin-hint' => 'Bitte kontaktiere Deinen Administrator.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Fehlerdetails anzeigen',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Fehlerdetails verbergen',
	'action-responsibleeditors-viewspecialpage' => 'Seiten anzusehen, die mit dem responsibleeditors-viewspecialpage-Recht geschützt sind',
	'bs-viewtagerrorlist-legend' => '$1 - Fehler',
	'bs-readonly' => 'Die Datenbank ist vorübergehend für Neueinträge und Änderungen gesperrt. Bitte versuche es später noch einmal. Grund der Sperrung: $1.',
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
	'bs-filesystemhelper-upload-local-error-stash-file' => 'Die Datei konnte nicht in den Uploadstash verschoben werden.',
	'bs-filesystemhelper-upload-local-error-create' => 'Die Datei konnte im Wiki nicht angelegt werden.',
	'bs-navigation-instructions' => 'Anleitung',
	'bs-navigation-support' => 'Support',
	'bs-navigation-contact' => 'Kontakt',
);

/** German (formal address) (Deutsch (Sie-Form)‎)
 */
$messages['de-formal'] = array(
	'bs-email-footer' => 'Dies ist eine automatisch generierte E-Mail. Bitte antworten Sie nicht auf diese E-Mail!',
	'bs-userpreferences-link-title' => 'Zeigt Ihre persönlichen Benutzereinstellungen an.',
	'bs-exception-view-admin-hint' => 'Bitte kontaktieren Sie Ihren Administrator.',
	'bs-imageofotheruser' => 'Sie sind nicht berechtigt, ein Bild für einen anderen Benutzer hochzuladen.',
);

/** French (français)
 * @author Gomoko
 */
$messages['fr'] = array(
	'bs-ns_main' => '(Pages)',
	'bs-ns_all' => '(tous)',
	'bs-tab_navigation' => 'Navigation',
	'bs-tab_focus' => 'Centre d’intérêt',
	'bs-tab_admin' => 'Admin',
	'bs-userbar_loginswitch_logout' => 'Déconnexion',
	'bs-userbar_loginswitch_login' => 'Connexion',
	'bs-extended-search-tooltip-fulltext' => 'Recherche en texte intégral',
	'bs-extended-search-textfield-defaultvalue' => 'Recherche en cours…',
	'bs-extended-search-textfield-tooltip' => 'Rechercher dans le wiki',
	'bs-no-information-available' => 'Aucune information disponible',
	'bs-years-duration' => '{{PLURAL:$1|une année|$1 années}}',
	'bs-months-duration' => '{{PLURAL:$1|un mois|$1 mois}}',
	'bs-weeks-duration' => '{{PLURAL:$1|une semaine|$1 semaines}}',
	'bs-days-duration' => '{{PLURAL:$1|un jour|$1 jours}}',
	'bs-hours-duration' => '{{PLURAL:$1|une heure|$1 heures}}',
	'bs-mins-duration' => '{{PLURAL:$1|une minute|$1 minutes}}',
	'bs-secs-duration' => '{{PLURAL:$1|une seconde|$1 secondes}}',
	'bs-two-units-ago' => 'il y a $1 et $2',
	'bs-one-unit-ago' => 'il y a $1',
	'bs-now' => 'maintenant',
	'bs-email-greeting-receiver' => '{{GENDER:$1|Bonjour M. $1|Bonjour Mme $1|Bonjour $1}},',
	'bs-email-greeting-no-receiver' => 'Bonjour,',
	'bs-email-footer' => 'Ce message a été généré automatiquement. Veuillez ne pas répondre à ce courriel.',
	'bs-userpagesettings-legend' => 'Paramètres utilisateur',
	'bs-userpreferences-link-text' => 'Paramètres utilisateurs avancés',
	'bs-userpreferences-link-title' => 'Afficher vos paramètres utilisateur personnels',
	'bs-exception-view-heading' => 'Une erreur s’est produite',
	'bs-exception-view-text' => 'Lors du traitement de votre requête, l’erreur suivante s’est produite :',
	'bs-exception-view-admin-hint' => 'Veuillez contacter votre administrateur système.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Afficher les détails de l’erreur',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Masquer les détails de l’erreur',
	'action-responsibleeditors-viewspecialpage' => 'afficher les pages qui sont protégées avec le droit « ResponsibleEditors-Viewspecialpage »',
	'bs-viewtagerrorlist-legend' => '$1 - Erreur',
	'bs-readonly' => 'La base de données est actuellement verrouillée pour de nouvelles entrées et d’autres modifications, probablement par une routine de maintenance de la base de données, après laquelle tout reviendra à la normale. L’administrateur qui l’a verrouillée a offert l’explication suivante : $1',
	'bs-imageofotheruser' => 'Vous n’êtes pas autorisé à importer une image pour un autre utilisateur.',
	'bs-pref-sortalph' => 'Trier les espaces de nom par ordre alphabétique',
	'bs-permissionerror' => 'Erreur de droit !',
	'bs-filesystemhelper-no-directory' => '<code>$1</code> n’est pas un répertoire valide.',
	'bs-filesystemhelper-has-path-traversal' => 'Traversée de chemin détectée !',
	'bs-filesystemhelper-file-not-exists' => 'Le fichier <code>$1</code> n’existe pas.',
	'bs-filesystemhelper-file-copy-error' => 'Le fichier <code>$1</code> n’a pas pu être copié.',
	'bs-filesystemhelper-file-already-exists' => 'Le fichier <code>$1</code> existe déjà.',
	'bs-filesystemhelper-file-delete-error' => 'Le fichier <code>$1</code> n’a pas pu être supprimé.',
	'bs-filesystemhelper-folder-already-exists' => 'Le répertoire <code>$1</code> existe déjà.',
	'bs-filesystemhelper-folder-copy-error' => 'Le répertoire <code>$1</code> n’a pas pu être renommé.',
	'bs-filesystemhelper-folder-not-exists' => 'Le répertoire <code>$1</code> n’existe pas.',
	'bs-filesystemhelper-upload-err-code' => 'Le fichier n’a pas pu être téléchargé : $1',
	'bs-filesystemhelper-upload-wrong-ext' => 'Le fichier n’a pas l’extension requise : $1.',
	'bs-filesystemhelper-upload-unsupported-type' => 'Ce type de fichier n’est pas pris en charge.',
	'bs-filesystemhelper-save-base64-error' => 'Le fichier n’a pas pu être enregistré.',
	'bs-filesystemhelper-upload-base64-error' => 'Le fichier n’a pas pu être téléchargé.',
	'bs-filesystemhelper-upload-local-error-stash-file' => 'Le fichier n’a pas pu être déplacé vers la réserve de téléchargement.',
	'bs-filesystemhelper-upload-local-error-create' => 'Le fichier n’a pas pu être créé dans le wiki.',
	'bs-navigation-instructions' => 'Instructions',
	'bs-navigation-support' => 'Soutien',
	'bs-navigation-contact' => 'Contact',
);

/** Japanese (日本語)
 * @author Shirayuki
 */
$messages['ja'] = array(
	'bs-ns_main' => '(ページ)',
	'bs-ns_all' => '(すべて)',
	'bs-userbar_loginswitch_logout' => 'ログアウト',
	'bs-userbar_loginswitch_login' => 'ログイン',
	'bs-extended-search-tooltip-fulltext' => '全文検索',
	'bs-viewtagerrorlist-legend' => '$1 - エラー',
);

/** Kazakh (Cyrillic script) (қазақша (кирил)‎)
 * @author Габдулгани НИШ ХБН
 */
$messages['kk-cyrl'] = array(
	'bs-ns_main' => 'Беттер',
	'bs-tab_focus' => 'орталығы',
	'bs-userbar_loginswitch_logout' => 'шығу',
	'bs-userbar_loginswitch_login' => 'Кіру',
	'bs-no-information-available' => 'Мәлімет жоқ',
	'bs-days-duration' => '{{PLURAL:$1|бір күн|$1 күндер}}',
	'bs-hours-duration' => '{{PLURAL:$1|бір сағат|$1 сағаттар}}',
	'bs-mins-duration' => '{{PLURAL:$1|бір минут| $1 минуттар}}',
	'bs-two-units-ago' => '$1 -$2 ден кем',
	'bs-email-greeting-receiver' => '{{GENDER:$1|Сәлем, $1 мырза|Сәлем, $1 ханым| Сәлем , $1}}',
	'bs-email-greeting-no-receiver' => 'сәлем,',
	'bs-email-footer' => 'Бұл хабарлау автоматты түрінде жасалған. Жауап бермеңіз',
	'bs-userpreferences-link-title' => 'Сіздің жеке деректеріңізді көрсету',
	'bs-exception-view-text' => 'Сіздің сауалыңызды ондегенде келесі қате табылды:',
	'bs-imageofotheruser' => 'Сен басқа қатысушының орнына  суретті жүктей алмайсын',
	'bs-filesystemhelper-file-copy-error' => 'Бұл файлдың <code>$1</code> көшірмесін алуға болмайды',
	'bs-filesystemhelper-upload-err-code' => 'Файлдың жүктеле алмайды, себебі:', # Fuzzy
	'bs-filesystemhelper-upload-base64-error' => 'Файл жүктеле алмайды',
	'bs-filesystemhelper-upload-local-error-create' => 'Бұл файл  уики-де жасалынбайды',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'bs-ns_main' => '(Säiten)',
	'bs-weeks-duration' => '{{PLURAL:$1|eng Woch|$1 Wochen}}',
	'bs-mins-duration' => '{{PLURAL:$1|eng Minutt|$1 Minutten}}',
	'bs-now' => 'elo',
	'bs-userpagesettings-legend' => 'Benotzerastellungen',
	'bs-navigation-contact' => 'Kontakt',
);

/** Macedonian (македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'bs-ns_main' => '(Страници)',
	'bs-ns_all' => '(сите)',
	'bs-tab_navigation' => 'Навигација',
	'bs-tab_focus' => 'Задршка',
	'bs-tab_admin' => 'Админ',
	'bs-userbar_loginswitch_logout' => 'Одјава',
	'bs-userbar_loginswitch_login' => 'Најава',
	'bs-extended-search-tooltip-fulltext' => 'Пребарување по цел текст',
	'bs-extended-search-textfield-defaultvalue' => 'Пребарување...',
	'bs-extended-search-textfield-tooltip' => 'Пребарајте го викито',
	'bs-no-information-available' => 'Нема информации на располагање',
	'bs-years-duration' => '{{PLURAL:$1|една година|$1 години}}',
	'bs-months-duration' => '{{PLURAL:$1|еден месец|$1 месеци}}',
	'bs-weeks-duration' => '{{PLURAL:$1|$1 недела|$1 недели}}',
	'bs-days-duration' => '{{PLURAL:$1|еден ден|$1 дена}}',
	'bs-hours-duration' => '{{PLURAL:$1|еден час|$1 часа}}',
	'bs-mins-duration' => '{{PLURAL:$1|една минута|$1 минути}}',
	'bs-secs-duration' => '{{PLURAL:$1|една секунда|$1 секунди}}',
	'bs-two-units-ago' => 'пред $1 и $2',
	'bs-one-unit-ago' => 'пред $1',
	'bs-now' => 'сега',
	'bs-email-greeting-receiver' => '{{GENDER:$1|Здраво г-дине $1|Здраво г-ѓо $1|Здраво $1}},',
	'bs-email-greeting-no-receiver' => 'Здраво,',
	'bs-email-footer' => 'Писмово е автоматски создадено. Не одговарајте на него.',
	'bs-userpagesettings-legend' => 'Кориснички нагодувања',
	'bs-userpreferences-link-text' => 'Уште кориснички нагодувања',
	'bs-userpreferences-link-title' => 'Прикажи мои лични кориснички нагодувања',
	'bs-exception-view-heading' => 'Се појави грешка',
	'bs-exception-view-text' => 'Обработувајќи го барањето, се појави следнава грешка:',
	'bs-exception-view-admin-hint' => 'Обратете се кај администраторот.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Прикажи пордобности за грешката',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Скриј подробности за грешката',
	'action-responsibleeditors-viewspecialpage' => 'прегледај страници заштитени со правото „ResponsibleEditors-Viewspecialpage“',
	'bs-viewtagerrorlist-legend' => '$1 — Грешка',
	'bs-readonly' => 'Базата е моментално заклучена за нови статии и други измени, најверојатно како рутинска проверка, по што ќе се врати во нормална состојба. Администраторот кој ја заклучи го понуди следното образложение: $1',
	'bs-imageofotheruser' => 'Не ви е дозволено да подигате слики во име на друг корисник.',
	'bs-pref-sortalph' => 'Подреди ги именските простори по азбучен редослед',
	'bs-permissionerror' => 'Грешка со дозволите!',
	'bs-filesystemhelper-no-directory' => '<code>$1</code> не претставува важечка папка.',
	'bs-filesystemhelper-has-path-traversal' => 'Утврдено е пресекување на патеката.',
	'bs-filesystemhelper-file-not-exists' => 'Податотеката <code>$1</code> не постои.',
	'bs-filesystemhelper-file-copy-error' => 'Не можев да ја ископирам податотеката <code>$1</code>.',
	'bs-filesystemhelper-file-already-exists' => 'Податотеката <code>$1</code> веќе постои.',
	'bs-filesystemhelper-file-delete-error' => 'Не можам да ја избришам податотеката <code>$1</code>.',
	'bs-filesystemhelper-folder-already-exists' => 'Папката <code>$1</code> веќе постои.',
	'bs-filesystemhelper-folder-copy-error' => 'Не можев да ја преименувам папката <code>$1</code>.',
	'bs-filesystemhelper-folder-not-exists' => 'Папката <code>$1</code> не постои.',
	'bs-filesystemhelper-upload-err-code' => 'Не можев да ја подигнам податотеката: $1',
	'bs-filesystemhelper-upload-wrong-ext' => 'Податотеката ја нема потребната наставка: $1.',
	'bs-filesystemhelper-upload-unsupported-type' => 'Овој податотечен тип не е поддржан.',
	'bs-filesystemhelper-save-base64-error' => 'Не можев да ја зачувам податотеката.',
	'bs-filesystemhelper-upload-base64-error' => 'Не можев да ја подигнам податотеката.',
	'bs-filesystemhelper-upload-local-error-stash-file' => 'Не можев да ја преместан податотеката во оставата на подигања.',
	'bs-filesystemhelper-upload-local-error-create' => 'Не можев да ја создадам податотеката на викито.',
	'bs-navigation-instructions' => 'Напатствија',
	'bs-navigation-support' => 'Поддршка',
	'bs-navigation-contact' => 'Контакт',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 */
$messages['nl'] = array(
	'bs-ns_main' => "(Pagina's)",
);

/** Polish (polski)
 * @author Chrumps
 */
$messages['pl'] = array(
	'bs-ns_main' => '(Strony)',
	'bs-ns_all' => '(wszystkie)',
	'bs-tab_navigation' => 'Nawigacja',
	'bs-userbar_loginswitch_logout' => 'Wyloguj',
	'bs-userbar_loginswitch_login' => 'Zaloguj się',
	'bs-extended-search-tooltip-fulltext' => 'Wyszukiwanie pełnotekstowe',
	'bs-extended-search-textfield-defaultvalue' => 'Wyszukiwanie...',
	'bs-no-information-available' => 'Brak informacji',
	'bs-years-duration' => '{{PLURAL:$1|1 rok|$1 lata|$1 lat}}',
	'bs-months-duration' => '{{PLURAL:$1|1 miesiąc|$1 miesiące|$1 miesięcy}}',
	'bs-weeks-duration' => '{{PLURAL:$1|1 tydzień|$1 tygodnie|$1 tygodni}}',
	'bs-days-duration' => '{{PLURAL:$1|1 dzień|$1 dni}}',
	'bs-hours-duration' => '{{PLURAL:$1|1 godzina|$1 godziny|$1 godzin}}',
	'bs-mins-duration' => '{{PLURAL:$1|1 minuta|$1 minuty|$1 minut}}',
	'bs-secs-duration' => '{{PLURAL:$1|1 sekunda|$1 sekundy|$1 sekund}}',
	'bs-two-units-ago' => '$1 i $2 temu',
	'bs-one-unit-ago' => '$1 temu',
	'bs-now' => 'teraz',
	'bs-userpagesettings-legend' => 'Ustawienia użytkownika',
	'bs-exception-view-heading' => 'Wystąpił błąd',
	'bs-exception-view-text' => 'Podczas przetwarzania żądania wystąpił następujący błąd:',
	'bs-exception-view-admin-hint' => 'Skontaktuj się z administratorem systemu.',
	'bs-viewtagerrorlist-legend' => '$1 - Błąd',
	'bs-pref-sortalph' => 'Alfabetycznie sortowanie przestrzeni nazw',
	'bs-permissionerror' => 'Błąd uprawnień!',
	'bs-filesystemhelper-file-not-exists' => 'Plik <code>$1</code> nie istnieje.',
	'bs-filesystemhelper-file-already-exists' => 'Plik <code>$1</code> już istnieje.',
	'bs-filesystemhelper-folder-already-exists' => 'Folder <code>$1</code> już istnieje.',
	'bs-filesystemhelper-folder-not-exists' => 'Folder <code>$1</code> nie istnieje.',
	'bs-navigation-instructions' => 'Instrukcje',
);

/** Russian (русский)
 * @author Okras
 */
$messages['ru'] = array(
	'bs-ns_main' => '(Страницы)',
	'bs-ns_all' => '(все)',
	'bs-tab_navigation' => 'Навигация',
	'bs-tab_admin' => 'Админ',
	'bs-userbar_loginswitch_logout' => 'Выйти',
	'bs-userbar_loginswitch_login' => 'Войти',
	'bs-extended-search-tooltip-fulltext' => 'Полнотекстовый поиск',
	'bs-extended-search-textfield-defaultvalue' => 'Поиск…',
	'bs-no-information-available' => 'Информация отсутствует',
	'bs-years-duration' => '{{PLURAL:$1|один год|$1 лет|$1 года}}',
	'bs-months-duration' => '{{PLURAL:$1|один месяц|$1 месяцев|$1 месяца}}',
	'bs-weeks-duration' => '{{PLURAL:$1|одну неделю|$1 недель|$1 недели}}',
	'bs-days-duration' => '{{PLURAL:$1|один день|$1 дней|$1 дня}}',
	'bs-hours-duration' => '{{PLURAL:$1|один час|$1 часов|$1 часа}}',
	'bs-mins-duration' => '{{PLURAL:$1|одну минуту|$1 минут|$1 минуты}}',
	'bs-secs-duration' => '{{PLURAL:$1|одну секунду|$1 секунд|$1 секунды}}',
	'bs-two-units-ago' => '$1 и $2 назад',
	'bs-one-unit-ago' => '$1 назад',
	'bs-now' => 'только что',
	'bs-email-greeting-no-receiver' => 'Привет,',
	'bs-email-footer' => 'Это сообщение было создано автоматически. Пожалуйста, не отвечайте на это письмо.',
	'bs-userpagesettings-legend' => 'Настройки пользователя',
	'bs-userpreferences-link-text' => 'Дополнительные настройки пользователя',
	'bs-exception-view-heading' => 'Произошла ошибка',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Показать сведения об ошибке',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Скрыть сведения об ошибке',
	'bs-filesystemhelper-upload-unsupported-type' => 'Этот тип файла не поддерживается.',
	'bs-filesystemhelper-save-base64-error' => 'Файл не может быть сохранён.',
	'bs-filesystemhelper-upload-base64-error' => 'Файл не может быть загружен.',
	'bs-filesystemhelper-upload-local-error-create' => 'Файл не может быть создан в вики.',
	'bs-navigation-instructions' => 'Инструкции',
	'bs-navigation-support' => 'Поддержка',
);

/** Scots (Scots)
 * @author John Reid
 */
$messages['sco'] = array(
	'bs-ns_main' => '(Pages)',
	'bs-ns_all' => '(aw)',
	'bs-tab_navigation' => 'Naveegation',
	'bs-tab_focus' => 'Focus',
	'bs-tab_admin' => 'Admeen',
	'bs-userbar_loginswitch_logout' => 'Log oot',
	'bs-userbar_loginswitch_login' => 'Log in',
	'bs-extended-search-tooltip-fulltext' => 'Fultex rake',
	'bs-extended-search-textfield-defaultvalue' => 'Rake...',
	'bs-extended-search-textfield-tooltip' => 'Rake the wiki',
	'bs-no-information-available' => 'Nae information available',
	'bs-years-duration' => '{{PLURAL:$1|yin year|$1 years}}',
	'bs-months-duration' => '{{PLURAL:$1|yin month|$1 months}}',
	'bs-weeks-duration' => '{{PLURAL:$1|yin week|$1 weeks}}',
	'bs-days-duration' => '{{PLURAL:$1|yin day|$1 days}}',
	'bs-hours-duration' => '{{PLURAL:$1|yin hoor|$1 hoors}}',
	'bs-mins-duration' => '{{PLURAL:$1|yin minute|$1 minutes}}',
	'bs-secs-duration' => '{{PLURAL:$1|yin seicont|$1 seiconts}}',
	'bs-two-units-ago' => '$1 n $2 syne',
	'bs-one-unit-ago' => '$1 syne',
	'bs-now' => 'nou',
	'bs-email-greeting-receiver' => '{{GENDER:$1|Hallo Mr $1|Hallo Mrs $1|Hallo $1}},',
	'bs-email-greeting-no-receiver' => 'Hallo,',
	'bs-email-footer' => 'This message wis generatit autæmateecallie. Please dinna replie til this email.',
	'bs-userpagesettings-legend' => 'Uiser settins',
	'bs-userpreferences-link-text' => 'Mair uiser settins',
	'bs-userpreferences-link-title' => 'Displey yer personal uiser settins',
	'bs-exception-view-heading' => 'Ae mistak happent',
	'bs-exception-view-text' => 'While processin yer request the follaein mistak happent:',
	'bs-exception-view-admin-hint' => 'Please contact yer system admeenistrater.',
	'bs-exception-view-stacktrace-toggle-show-text' => 'Shaw the mistak details',
	'bs-exception-view-stacktrace-toggle-hide-text' => 'Skauk the mistak details',
	'action-responsibleeditors-viewspecialpage' => 'see the pages that ar protectit wi the "ResponsibleEediters-Seebyordinairpage" richt',
	'bs-viewtagerrorlist-legend' => '$1 - Mistak',
	'bs-readonly' => 'The database is nou lockit til new entries n ither modifeecations, proably fer routine database maintenance, after this it will be back til normal. The admeenistrater that lockt it affered this explanation: $1',
	'bs-imageofotheruser' => "Ye'r na permitit tae uplaid aen eemage fer anither uiser.",
	'bs-pref-sortalph' => 'Sort namespaces alphabeticlie',
	'bs-permissionerror' => 'Permission mistak!',
	'bs-filesystemhelper-no-directory' => '<code>$1</code> isna ae valid directerie.',
	'bs-filesystemhelper-has-path-traversal' => 'Path traversal detectit!',
	'bs-filesystemhelper-file-not-exists' => 'The file <code>$1</code> disna exeest.',
	'bs-filesystemhelper-file-copy-error' => 'The file <code>$1</code> coud na be copied.',
	'bs-filesystemhelper-file-already-exists' => 'The file <code>$1</code> awreadie exeests.',
	'bs-filesystemhelper-file-delete-error' => 'The file <code>$1</code> coudna be delytit.',
	'bs-filesystemhelper-folder-already-exists' => 'The fauder <code>$1</code> awreadie exeests.',
	'bs-filesystemhelper-folder-copy-error' => 'The fauder <code>$1</code> coudna be renamed.',
	'bs-filesystemhelper-folder-not-exists' => 'The fauder <code>$1</code> disna exeest.',
	'bs-filesystemhelper-upload-err-code' => 'The file coudna be uplaidit: $1',
	'bs-filesystemhelper-upload-wrong-ext' => 'The file disna hae the needit extension: $1.',
	'bs-filesystemhelper-upload-unsupported-type' => 'This file type isna supportit.',
	'bs-filesystemhelper-save-base64-error' => 'The file coudna be hained.',
	'bs-filesystemhelper-upload-base64-error' => 'The file coudna be uplaidit.',
	'bs-filesystemhelper-upload-local-error-stash-file' => 'The file coudna be muived til the uplaid stash.',
	'bs-filesystemhelper-upload-local-error-create' => 'The file coud na be cræftit in the wiki.',
	'bs-navigation-instructions' => 'Instructions',
	'bs-navigation-support' => 'Support',
	'bs-navigation-contact' => 'Contact',
);
