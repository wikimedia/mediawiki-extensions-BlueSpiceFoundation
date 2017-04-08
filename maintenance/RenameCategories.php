<?php
#error_reporting(-1);
#ini_set('display_errors', 1);
#if (PHP_OS == "WINNT") exec("chcp 65001"); # doesn't seem to work - do it manually
if ($argc != 2) exit("Syntax: {$argv[0]} filename");
$filename = $argv[1];
if (!is_readable($filename)) exit("File '$filename' could not be read.");
$rawlist = file_get_contents($filename);
preg_match_all('|^(.*?);(.*?)$|m', $rawlist, $matches);
#var_dump($matches);exit;

$oldcats = $matches[1];
$newcats = $matches[2];

$numcats = count($oldcats);
if ($numcats < 1) exit("No categories found in file.");

echo "Found $numcats categories to replace:" . PHP_EOL . PHP_EOL;
for ($i=0; $i<$numcats; $i++){
	echo 1+$i.": {$oldcats[$i]} --> {$newcats[$i]}" . PHP_EOL;
}
if (PHP_OS == "WINNT") {
	echo PHP_EOL;
	echo "NOTE: Your Windows prompt may show garbled characters. The script should still work.";
}
echo PHP_EOL;
echo "Are you sure you want to do this?  Type 'yes' to continue: ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'yes') exit("ABORTING!".PHP_EOL);

$oldcatspatterns = array_map('old_to_regex', $oldcats);

function old_to_regex($rawcat) {
	$rawcat = trim($rawcat);
	$rawcat = preg_quote($rawcat, '#'); # escape special regex characters plus the hash delimiter we'll use
	$rawcat = '\[\[(:?Kategorie|:?Category):' . $rawcat . '\]\]'; # note: links to category pages begin with [[:
	$rawcat = preg_replace('# |_#', '[ _]', $rawcat); # spaces in categories may occur as space or underscore
	$rawcat = '#' . $rawcat . '#';
	return $rawcat;
}

var_dump($oldcatspatterns);

$newcatspatterns = array_map('new_to_pattern', $newcats);

function new_to_pattern($rawcat) {
	$rawcat = trim($rawcat);
	$rawcat = '[[$1:' . $rawcat . ']]';
	return $rawcat;
}

var_dump($newcatspatterns);

$oldtitlepatterns = array_map('oldtitle_to_regex', $oldcats);

function oldtitle_to_regex($rawcat) {
	$rawcat = trim($rawcat);
	$rawcat = preg_quote($rawcat, '#'); # escape special regex characters plus the hash delimiter we'll use
	$rawcat = preg_replace('# |_#', '[ _]', $rawcat); # spaces in cats may occur as space or underscore
	$rawcat = '#' . $rawcat . '#';
	return $rawcat;
}

var_dump($oldtitlepatterns);

$newtitlepatterns = array_map('newtitle_to_pattern', $newcats);

function newtitle_to_pattern($rawcat) {
	$rawcat = trim($rawcat);
	return $rawcat;
}

var_dump($newtitlepatterns);

# rename categories in all articles & rename articles in category namespace

# The following part is copied from hw_massedit.php
# ---------------------- snip ---------------------

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


//$optionsWithArgs = array( 'u', 's' );

// include MediaWiki's command line tools
require_once( 'BSMaintenance.php' );


// TODO: Das in ein options.php file tun, das per Parameter aufgerufen wird

$userName = 'WikiBot';
$summary = 'Renaming of categories';
$minor = false;
$bot = true;
$autoSummary = false;
$noRC = true;

//$text_exclude = array(
// 					'/\[\[Kategorie:Englisch\]\]/',
//					'/\[\[Category:Translation\]\]/'
//				 );
//$text_include = array(
//					'/test test/'
//				);
//$namespace_include = array( 10 );
//$namespace_exclude = array( NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK );
//$title_include = array( '/Datev/', '/Hauptseite/' );
//$title_include = array( '|NZ/.*?|' );
//$title_include = array( '|NZ/.*?|' );

#$mode = 'grep';
$mode = 'replace';
//$mode = 'append';
//$mode = 'prefix';
//$mode = 'delete';
$testing = false;		// do not apply the changes
$verbose = true;  		// print modified text in test mode

// Parameters for text manipulation modes:
// ---------------------------------------

//$prefix_text = "testsetset\n";
//$append_text = '\n[[Category:Translate]]';
//$delete_text = '/\[\[Category:English\]\]/';

#$grep_regex = '|<diaslink(.*?)>|';

#$replace_search = '|<diaslink url="https://dias.intranet.de/(.*?)"|';
#$replace_with   =  '<diaslink url="https://dias-qa.eu/$1"';

$replace_search = $oldcatspatterns;
$replace_with   = $newcatspatterns;

// Parameters for title manipulation modes:
// ----------------------------------------

//$move_from = '|^NZ/(.*)$|';
//$move_to = 'NZ:$1';


// ---- End options

// Check valid user
$wgUser = User::newFromName( $userName );
if ( !$wgUser ) hw_error("Invalid username");

if ( $wgUser->isAnon() ) {
	$wgUser->addToDatabase();
}

// Check conditions for validity
if (isset($namespace_include) && isset($namespace_exclude)) hw_error('You cannot include and exclude namespaces at the same time');
if (isset($text_exclude) && isset($text_include)) hw_error('You cannot include and exclude namespaces at the same time');
if (isset($title_exclude) && isset($title_include)) hw_error('You cannot include and exclude titles at the same time');
if (isset($text_exclude) && isset($text_include)) hw_error('You cannot include and exclude namespaces at the same time');

// Get titles
// Namespace conditions
$token = '';
$qry_ns = '';
$namespace = array();
if (isset($namespace_include)) { $token = 'IN '; $namespace = $namespace_include; }
if (isset($namespace_exclude)) { $token = 'NOT IN '; $namespace = $namespace_exclude; }
if ($token) $qry_ns = 'page_namespace '.$token.' ("'.implode('","', $namespace).'")';

$dbw =& wfGetDB( DB_MASTER );
$res = $dbw->select('page', 'page_title, page_namespace, page_id', $qry_ns, 'Database::select', array('order by' => 'page_title'));

$wgGroupPermissions['*']['suppressredirect'] = true;
$wgGroupPermissions['*']['autoreview'] = true;
$wgFlaggedRevsAutoReview = true;

$matches = 0;
$inarticlematches = 0;

while ($row = mysql_fetch_array($res->result))
{

	$cur_title = $row['page_title'];
	$cur_title_ns = MWNamespace::getCanonicalName($row['page_namespace']);
	$cur_title_ns = ($cur_title_ns) ? "$cur_title_ns:" : "";
	print "$cur_title_ns$cur_title\n=======================================================\n";

	// Title conditions
	$title_conds = array();
	if (isset($title_include)) { $title_conds = $title_include; $match = true; }
	if (isset($title_exclude)) { $title_conds = $title_exclude; $match = false; }
	$skip = true;
	foreach ($title_conds as $cond)
		if (preg_match($cond, $cur_title)==$match) $skip = false;
	if ((isset($title_include) || isset($title_exclude)) && ($skip))
	{
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
	$wikipage = WikiPage::factory( $title );
	$text = ContentHandler::getContentText( $wikipage->getContent() );
	if ($text == '') echo 'empty!';

	// Text conditions
	$text_conds = array();
	if (isset($text_include)) { $text_conds = $text_include; $match = false; }
	if (isset($text_exclude)) { $text_conds = $text_exclude; $match = true; }
	$skip = false;
	foreach ($text_conds as $cond)
		if (preg_match($cond, $text) == $match) $skip = true;
	if ((isset($text_include) || isset($text_exclude)) && ($skip))
	{
		print "Skipped based on text exclude condition.\n";
		continue;
	}

	// this part is for text modification only (append, prefix, delete, replace)
	if (in_array($mode, array('append','prefix','delete','replace')))
	{
		# Modify the text
		$old_text = $text;

		if ($mode == 'append')
			$text .= $append_text;
		else if ($mode == 'prefix')
			$text = $prefix_text.$text;
		else if ($mode == 'delete')
		{
			$text = preg_replace($delete_text, '', $text);
		}
		else if ($mode == 'replace')
		{
			$text = preg_replace($replace_search, $replace_with, $text, -1, $repcount);
		}

		if ($old_text == $text)
		{
			print "No modification neccessary.\n";
			continue;
		}
		$matches++;
		# Do the edit
		print "Modifying $cur_title_ns$cur_title ... ($repcount matches) ";
		$inarticlematches += $repcount;

		// Only testing
		if ($testing)
		{
			print "testing.\n";
			if ($verbose) print $text."\n--------------------------------------------------------------------------------\n\n";
			continue;
		}

		// Actual modification
		$success = $article->doEditContent(
			ContentHandler::makeContent( $text, $title ),
			$summary,
			( $minor ? EDIT_MINOR : 0 ) |
			( $bot ? EDIT_FORCE_BOT : 0 ) |
			( $autoSummary ? EDIT_AUTOSUMMARY : 0 ) |
			( $noRC ? EDIT_SUPPRESS_RC : 0 )
		);
		if ( $success ) {
			print "done\n";
		} else {
			print "failed\n";
		}
	} // end of text manipulation modes

	// this part is for other modes (e.g. "move")
	switch ($mode)
	{
		case 'move':
			$ns = $title->getNamespace();
			$new_title = preg_replace($move_from, $move_to, $cur_title);

			if ($new_title == $cur_title) continue;

			echo "Moving title \"$cur_title\" to \"$new_title\" (NS:$ns): ";

			// Only testing
			if ($testing)
			{
				echo "testing\n";
				continue;
			}

			// Actual modification
			$new_title_obj = Title::newFromText( $new_title );
			$success = $title->moveTo( $new_title_obj, false, '', false );
			echo $success ? "done\n" : "failed\n";
			break;

		case 'grep':
			$nmatches = preg_match_all( $grep_regex, $text, $grepmatches );
			if ($nmatches)
			{
				echo("MATCH! This article matches $nmatches times.\n");
				$matches++;
				$inarticlematches += $nmatches;
			}
		break;
	}

}

function hw_error($msg)
{
	echo 'ERROR: '.$msg;
	exit();
}

echo "\n\n------------\nArticles modified: $matches\n";
if ($mode="replace" || $mode="grep") echo "Total replacements: $inarticlematches\n";

# ---------------------- snap ---------------------

echo PHP_EOL;

# now for the category namespace
$dbw =& wfGetDB( DB_MASTER );
$res = $dbw->select('page', 'page_id, page_title', array('page_namespace' => NS_CATEGORY), 'Database::select', array('order by' => 'page_title'));
echo $dbw->numRows($res) . " articles in category namespace\n";
foreach ($res as $row) {
	$oldtitle = Title::newFromId($row->page_id);
	$oldtitletext = $oldtitle->getText();
	$oldarticle = new Article($oldtitle);
	//Article::fetchContent() is deprecated.
	//Replaced by WikiPage::getContent()::getNativeData()
	$oldwikipage = WikiPage::factory( $oldtitle );
	$oldarticlecontent = ContentHandler::getContentText( $oldwikipage->getContent() );
	$newtitletext = preg_replace($oldtitlepatterns, $newtitlepatterns, $oldtitletext, -1, $repcount);
	echo "$oldtitle" . PHP_EOL . "=========================" . PHP_EOL;
	if ($repcount > 0) {
		echo "Moving $oldtitletext to $newtitletext : ";
		if (!$testing) {
			$newtitle = Title::newFromText($newtitletext, NS_CATEGORY);
			$newarticle = new Article($newtitle);
			$savestat = $newarticle->doEditContent(
				ContentHandler::makeContent( $oldarticlecontent, $newtitle ),
				$summary,
				( $minor ? EDIT_MINOR : 0 ) |
				( $bot ? EDIT_FORCE_BOT : 0 ) |
				( $autoSummary ? EDIT_AUTOSUMMARY : 0 ) |
				( $noRC ? EDIT_SUPPRESS_RC : 0 )
			);
		        if ($savestat->isGood()) echo "moved successfully\n";
			else echo "failed moving\n";
			/*
			echo "Creating redirect from $oldtitletext to $newtitletext : ";
			$redirstat = $oldarticle->doEditContent(
				ContentHandler::makeContent( "#REDIRECT [[:$newtitle]]", $oldTitle ),
				$summary,
				( $minor ? EDIT_MINOR : 0 ) |
				( $bot ? EDIT_FORCE_BOT : 0 ) |
				( $autoSummary ? EDIT_AUTOSUMMARY : 0 ) |
				( $noRC ? EDIT_SUPPRESS_RC : 0 )
			);
		        if ($redirstat->isGood()) echo "successful\n";
			else echo "failed\n";
			*/
			echo "Deleting $oldtitletext : ";
			$delstat = $oldarticle->doDeleteArticle( $summary, $noRC );
		        if ($delstat) echo "successful\n"; # doesn't work
			else echo "failed\n";
		}
		else echo " ... testing" . PHP_EOL;
	}
}
