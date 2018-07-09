<?php

/**
 * BsACTION
 *
 * Represents the different actions a http request can contain. Those are
 * equivalent to the allowed values of the "action" parameter within a
 * querystring.
 * With BlueSpice being a former MediaWiki framework those actions are very
 * similar to the ones described at
 * https://www.mediawiki.org/wiki/Manual:Parameters_to_index.php
 */
class BsACTION {
	const NONE = 0;
	/**
	 * BsACTION::LOAD_SPECIALPAGE
	 * Has to be set, when a extension provides a specialpage
	 */
	const LOAD_SPECIALPAGE = 1;
	/**
	 * BsACTION::LOAD_ON_API
	 * Has to be set, when a extension provides an api module
	 */
	const LOAD_ON_API = 2;
}
