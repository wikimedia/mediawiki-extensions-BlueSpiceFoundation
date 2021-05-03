<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

use BsNamespaceHelper;
use MWNamespace;
use Title;

class CategoryLinksHelper extends InternalLinksHelper {

	/**
	 *
	 * @param string $fullMatch
	 * @param string $leadingColon
	 * @param string $titleText
	 * @return \Title|null
	 */
	protected function makeTitleFromMatch( $fullMatch, $leadingColon, $titleText ) {
		if ( !empty( $leadingColon ) ) {
			return null;
		}
		$title = parent::makeTitleFromMatch(
			$fullMatch,
			$leadingColon,
			$titleText
		);
		if ( !$title ) {
			return $title;
		}
		if ( $title->getNamespace() !== NS_CATEGORY ) {
			return null;
		}
		return $title;
	}

	/**
	 *
	 * @param \Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 * @param bool $leadingColon
	 * @param string $separator
	 */
	protected function addTarget( \Title $target, $text, $addDuplicates, $leadingColon = true,
		$separator = "\n" ) {
		if ( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		parent::addTarget( $target, false, false, false, "\n" );
	}

	/**
	 * Parser functions like "{{#ask:[[Category:ABC]]}}" are replaced when removing
	 * category from page.
	 * So to preserve them they are masked with "###$id###" and saved in this array.
	 * These queries are restored to original view when adding category to page.
	 *
	 * @return array Array with information about what was replaced and replacement that was used.
	 * Has next structure:
	 * <dl>
	 *   <dt>Index 0</dt><dd>Original parser function</dd>
	 *   <dt>Index 1</dt><dd>Parser function replacement.
	 * 		Used as a key to return it to original view</dd>
	 * </dl>
	 * @see CategoryLinksHelper::unmaskParserFunctions()
	 */
	public function maskParserFunctions(): array {
		$replacedQueries = [];

		foreach ( $this->getTargets() as $match => $title ) {
			// Mask semantic queries to prevent them from changing
			$this->wikitext = preg_replace_callback(
				"#\{\{\#.*:\s*" . preg_quote( $match, '/' ) . ".*?}}#is",
				static function ( $matches ) use( &$replacedQueries ) {
					$id = count( $replacedQueries );
					$replacement = "###$id###";

					$replacedQueries[] = [ $matches[0], $replacement ];

					return $replacement;
				},
				$this->wikitext
			);
		}

		return $replacedQueries;
	}

	/**
	 * Gets all explicit categories, which exist in {@link CategoryLinksHelper::$wikitext}.
	 *
	 * @return string[] List of explicit category titles
	 */
	public function getExplicitCategories(): array {
		// Mask parser functions with category parameters to exclude them from search results
		$this->maskParserFunctions();

		// Pattern for Category tags
		$canonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
		$localNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		$pattern = "#\[\[($localNSName|$canonicalNSName):(.*?)(\|(.*?)|)\]\]#si";
		$matches = [];
		preg_match_all( $pattern, $this->wikitext, $matches, PREG_PATTERN_ORDER );

		$categories = [];
		// normalize
		foreach ( $matches[2] as $match ) {
			$categoryTitle = Title::newFromText( $match, NS_CATEGORY );
			if ( $categoryTitle instanceof Title === false ) {
				continue;
			}
			array_push( $categories, $categoryTitle->getText() );
		}

		return $categories;
	}

	/**
	 * Restores parser functions to original view after masking them.
	 * Must be used after {@link CategoryLinksHelper::maskParserFunctions()}.
	 *
	 * @param array $replacedQueries Array with information about replacements done.
	 * 	Got from {@link CategoryLinksHelper::maskParserFunctions()}
	 */
	public function unmaskParserFunctions( array $replacedQueries ) {
		// Replace semantic queries masks back with original queries
		$maskedQueryPattern = "\#\#\#([0-9]+)\#\#\#";

		$this->wikitext = preg_replace_callback(
			"#" . $maskedQueryPattern . "#",
			static function ( $matches ) use( $replacedQueries ) {
				return $replacedQueries[$matches[1]][0];
			},
			$this->wikitext
		);
	}

	/**
	 *
	 * @param \Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( \Title $target, $removeAllOccurrences ) {
		if ( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		$replacedQueries = $this->maskParserFunctions();

		parent::removeTarget( $target, $removeAllOccurrences );

		$this->unmaskParserFunctions( $replacedQueries );
	}
}
