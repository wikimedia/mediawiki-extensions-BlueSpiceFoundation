<?php

// TODO MRG20100810: Die Typen sind eigentlich Mediawiki-spezifisch. Ebenso die Actions weiter oben.
// Sollten wir die nicht zum Mediawiki-Teil packen?
class EXTTYPE {
	/**
	 * EXTTYPE::SPECIALPAGE
	 * Reserved for additions to MediaWiki Special Pages.
	 */
	const SPECIALPAGE = 'specialpage';
	/**
	 * EXTTYPE::PARSERHOOK
	 * Used if your extension modifies, complements, or replaces the parser functions in MediaWiki.
	 */
	const PARSERHOOK  = 'parserhook';
	/**
	 * EXTTYPE::VARIABLE
	 * Extension that add multiple functionality to MediaWiki.
	 */
	const VARIABLE    = 'variable';
	/**
	 * EXTTYPE::OTHER
	 * All other extensions.
	 */
	const OTHER       = 'other';
}
