<?php

$IP = dirname(dirname(__DIR__));

//TODO: rework all maintenance scripts to just use Maintenance.php
require_once( "$IP/maintenance/commandLine.inc" );
require_once( "$IP/maintenance/Maintenance.php" );

class BSMaintenance extends Maintenance {

	/**
	 * Throw an error to the user. Doesn't respect --quiet, so don't use
	 * this for non-error output
	 *
	 * BSMaintenance: Public to allow hook callbacks to write output.
	 * @param $err String: the error to display
	 * @param $die Int: if > 0, go ahead and die out using this int as the code
	 */
	public function error( $err, $die = 0 ) {
		parent::error( $err, $die );
	}

	/**
	 * Throw some output to the user. Scripts can call this with no fears,
	 * as we handle all --quiet stuff here
	 *
	 * BSMaintenance: Public to allow hook callbacks to write output.
	 * Adds a line break.
	 * @param $out String: the text to show to the user
	 * @param $channel Mixed: unique identifier for the channel. See
	 *     function outputChanneled.
	 */
	public function output( $out, $channel = null ) {
		$out .= "\n"; //MediaWiki outputs no linebreak in output(), but in error() it does...
		parent::output($out, $channel);
	}
}