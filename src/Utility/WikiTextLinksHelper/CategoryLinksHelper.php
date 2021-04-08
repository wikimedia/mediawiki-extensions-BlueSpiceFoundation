<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

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
	 *
	 * @param \Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( \Title $target, $removeAllOccurrences ) {
		if ( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		/**
		 * Semantic queries like "{{#ask:[[Category:ABC]]}}" are replaced when removing
		 * category from page.
		 * So to preserve them they are masked with "###$id###" and saved in this array.
		 * These queries are restored to original view when adding category to page.
		 * Index 0 => original query
		 * Index 1 => masked query
		 */
		$replacedQueries = [];

		foreach ( $this->getTargets() as $match => $title ) {
			if ( !$target->equals( $title ) ) {
				continue;
			}

			// Mask semantic queries to prevent them from changing
			$this->wikitext = preg_replace_callback(
				"#\{\{\#.*:\s*" . preg_quote( $match, '/' ) . ".*?}}#is",
				function ( $matches ) use( &$replacedQueries ) {
					$id = count( $replacedQueries );
					$replacement = "###$id###";

					$replacedQueries[] = [ $matches[0], $replacement ];

					return $replacement;
				},
				$this->wikitext
			);

			break;
		}

		parent::removeTarget( $target, $removeAllOccurrences );

		// Replace semantic queries masks back with original queries
		$maskedQueryPattern = "\#\#\#([0-9]+)\#\#\#";

		$this->wikitext = preg_replace_callback(
			"#" . $maskedQueryPattern . "#",
			function ( $matches ) use( $replacedQueries ) {
				return $replacedQueries[$matches[1]][0];
			},
			$this->wikitext
		);
	}
}
