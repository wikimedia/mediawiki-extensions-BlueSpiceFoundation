<?php

/*
This script is used to do mass edits on Mediawiki articles. Articles are selected via
- namespace
- content via exclusion or inclusion regex
- title via regex
(
  - category
)
Article text can be appended or replaced based on regular expressions.
The options are to be specified in a separate file which has to be included from the command line.

Usage:

?> php hw_massedit <optionfile>

A sample optionfile can be found in sample.options.php

This script is based loosely on maintenance/edit.php of MediaWiki.

*/

// include MediaWiki's command line tools
require_once 'BSMaintenance.php';

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\ContentHandler;
use MediaWiki\Content\TextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Title\Title;

$userName    = 'WikiBot';
$summary     = 'Some meaningful description';
$minor       = false;
$bot         = true;
$autoSummary = false;
$noRC        = true;

# =============
# TEXT FILTER
# =============

// $text_exclude = array(
// '/\[\[Kategorie:Englisch\]\]/',
// '/\[\[Category:Translation\]\]/'
// );
// $text_include = array(
// '/test test/'
// );

# ==================
# NAMESPACE FILTER
# ==================

$namespace_include = [ NS_MAIN ];
// $namespace_include = array( 10 );
// $namespace_exclude = array( NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK );

# ==================
# TITLE FILTER
# ==================

// $title_include = array( '/Datev/', '/Hauptseite/' );
// $title_include = array( '|NZ/.*?|' );

# ========
#  MODE
# ========

$mode = 'replace';
// $mode = 'move';
// $mode = 'append';
// $mode = 'prefix';
// $mode = 'delete';

// do not apply the changes
$testing = true;
// print modified text in test mode
$verbose = true;

# Parameters for text manipulation modes:
# ---------------------------------------

$replace_search = '/Vorlage:/';
$replace_with   = 'Template:';
// $prefix_text = "testsetset\n";
// $append_text = '\n[[Category:Translate]]';
// $delete_text = '/\[\[Category:English\]\]/';

# Parameters for title manipulation modes:
# ----------------------------------------

// $move_from = '|^NZ/(.*)$|';
// $move_to   = 'NZ:$1';

########################## Nothing to do past this point ############################

// Check valid user
$services = MediaWikiServices::getInstance();
$wgUser = $services->getUserFactory()->newFromName( $userName );
if ( !$wgUser ) {
	error( "Invalid username" );
}

if ( !$wgUser->isRegistered() ) {
	$wgUser->addToDatabase();
}

// Check conditions for validity
if ( isset( $namespace_include ) && isset( $namespace_exclude ) ) {
	error( 'You cannot include and exclude namespaces at the same time' );
}
if ( isset( $text_exclude ) && isset( $text_include ) ) {
	error( 'You cannot include and exclude namespaces at the same time' );
}
if ( isset( $title_exclude ) && isset( $title_include ) ) {
	error( 'You cannot include and exclude titles at the same time' );
}
if ( isset( $text_exclude ) && isset( $text_include ) ) {
	error( 'You cannot include and exclude namespaces at the same time' );
}

// Get titles
// Namespace conditions
$token = '';
$namespace = [];
if ( isset( $namespace_include ) ) {
	$token = 'IN ';
	$namespace = $namespace_include;
}
if ( isset( $namespace_exclude ) ) {
	$token = 'NOT IN ';
	$namespace = $namespace_exclude;
}
if ( $token ) {
	$qry_ns = 'page_namespace ' . $token . ' ("' . implode( '","', $namespace ) . '")';
}

$dbr = $services->getDBLoadBalancer()->getConnection( DB_REPLICA );
$res = $dbr->select(
	'page',
	'page_title, page_namespace, page_id',
	$qry_ns,
	__METHOD__,
	[ 'order by' => 'page_title' ]
);

$wgGroupPermissions['*']['suppressredirect'] = true;
$hits = 0;

$namespaceInfo = $services->getNamespaceInfo();
$wikiPageFactory = $services->getWikiPageFactory();

$user = $services->getService( 'BSUtilityFactory' )
	->getMaintenanceUser()->getUser();
foreach ( $res as $row ) {
	$row = (array)$row;
	$cur_title    = $row['page_title'];
	$cur_title_ns = $namespaceInfo->getCanonicalName( $row['page_namespace'] );
	$cur_title_ns = ( $cur_title_ns ) ? "$cur_title_ns:" : "";
	print "$cur_title_ns$cur_title\n-----------------\n";

	// Title conditions
	$title_conds = [];
	if ( isset( $title_include ) ) {
		$title_conds = $title_include;
		$match = true;
	}
	if ( isset( $title_exclude ) ) {
		$title_conds = $title_exclude;
		$match = false;
	}
	$skip = true;
	foreach ( $title_conds as $cond ) {
		if ( preg_match( $cond, $cur_title ) == $match ) {
			$skip = false;
		}
	}
	if ( ( isset( $title_include ) || isset( $title_exclude ) ) && ( $skip ) ) {
		print "Skipped based on title exclude condition.\n";
		continue;
	}

	// Check title for validity
	$title = Title::makeTitle( $row['page_namespace'], $cur_title );
	if ( !$title ) {
		print "Invalid title\n";
		continue;
	}

	// Fetch text
	$wikipage = $wikiPageFactory->newFromTitle( $title );
	$content = $wikipage->getContent();
	$text = ( $content instanceof TextContent ) ? $content->getText() : '';
	if ( $text == '' ) {
		echo 'empty!';
	}

	// Text conditions
	$text_conds = [];
	if ( isset( $text_include ) ) {
		$text_conds = $text_include;
		$match = false;
	}
	if ( isset( $text_exclude ) ) {
		$text_conds = $text_exclude;
		$match = true;
	}
	$skip = false;
	foreach ( $text_conds as $cond ) {
		if ( preg_match( $cond, $text ) == $match ) {
			$skip = true;
		}
	}
	if ( ( isset( $text_include ) || isset( $text_exclude ) ) && ( $skip ) ) {
		print "Skipped based on text exclude condition.\n";
		continue;
	}

	// this part is for text modification only (append, prefix, delete, replace)
	if ( in_array( $mode, [ 'append', 'prefix', 'delete', 'replace' ] ) ) {
		# Modify the text
		$old_text = $text;

		if ( $mode == 'append' ) {
			$text .= $append_text;
		} elseif ( $mode == 'prefix' ) {
			$text = $prefix_text . $text;
		} elseif ( $mode == 'delete' ) {
			$text = preg_replace( $delete_text, '', $text );
		} elseif ( $mode == 'replace' ) {
			$text = preg_replace( $replace_search, $replace_with, $text );
		}

		if ( $old_text == $text ) {
			print "No modification necessary.\n";
			continue;
		}
		# Do the edit
		print "Modifying ... ";
		$hits++;

		// Only testing
		if ( $testing ) {
			print "testing.\n";
			if ( $verbose ) {
				print $text . "\n------------------------------------------------------"
					. "--------------------------\n\n";
			}
			continue;
		}

		// Actual modification
		$updater = $wikipage->newPageUpdater( $user );
		$content = ContentHandler::makeContent( $text, $title );
		$updater->setContent( SlotRecord::MAIN, $content );
		$comment = CommentStoreComment::newUnsavedComment( $summary );
		$flags = ( $minor ? EDIT_MINOR : 0 ) |
			( $bot ? EDIT_FORCE_BOT : 0 ) |
			( $autoSummary ? EDIT_AUTOSUMMARY : 0 ) |
			( $noRC ? EDIT_SUPPRESS_RC : 0 );
		try {
			$updater->saveRevision( $comment, $flags );
		} catch ( Exception $e ) {
			print $e->getMessage();
		}
		if ( $updater->wasSuccessful() ) {
			print "done\n";
		} else {
			print "failed\n";
		}
		// end of text manipulation modes
	}

	// this part is for other modes (e.g. "move")
	switch ( $mode ) {
		case 'move':
			$ns = $title->getNamespace();
			$new_title = preg_replace( $move_from, $move_to, $cur_title );

			if ( $new_title == $cur_title ) {
				break;
			}

			echo "Moving title \"$cur_title\" to \"$new_title\" (NS:$ns): ";

			// Only testing
			if ( $testing ) {
				echo "testing\n";
				break;
			}

			// Actual modification
			$new_title_obj = Title::newFromText( $new_title );
			$success = $title->moveTo( $new_title_obj, false, '', false );
			echo $success ? "done\n" : "failed\n";
			$hits++;
			break;
	}

}

echo "\n\n-- " . ( $testing ? "Would have m" : "M" ) . "odified $hits articles.\n";

/**
 *
 * @param string $msg
 */
function error( $msg ) {
	echo 'ERROR: ' . $msg;
	exit();
}
