<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class InternalLinksHelper {

	/**
	 *
	 * @var string
	 */
	protected $wikitext = '';

	/**
	 *
	 * @param string &$wikitext
	 */
	public function __construct( &$wikitext ) {
		$this->wikitext =& $wikitext;
	}

	/**
	 *
	 * @return array
	 */
	protected function parse() {
		$replaced = $this->maskParserFunctions();
		$pattern = $this->getPattern();
		$matches = [];
		$matchCount = preg_match_all(
			$pattern,
			$this->wikitext,
			$matches,
			PREG_SET_ORDER
		);
		$links = [];
		foreach ( $matches as $match ) {
			[ $fullMatch, $leadingColon, $titleText ] = $match;
			$title = $this->makeTitleFromMatch(
				$fullMatch,
				$leadingColon,
				$titleText
			);
			if ( !$title ) {
				continue;
			}
			$links[$fullMatch] = $title;
		}
		$this->unmaskParserFunctions( $replaced );
		return $links;
	}

	/**
	 * Parser functions like "{{#ask:[[Category:ABC]]}}" will be recognized as internal links
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
		// Mask semantic queries to prevent them from changing
		$this->wikitext = preg_replace_callback(
			"#\{\{[^}]+\}\}#is",
			static function ( $matches ) use( &$replacedQueries ) {
				$id = count( $replacedQueries );
				$replacement = "###$id###";

				$replacedQueries[] = [ $matches[0], $replacement ];

				return $replacement;
			},
			$this->wikitext
		);

		return $replacedQueries;
	}

	/**
	 * Restores parser functions to original view after masking them.
	 * Must be used after {@link CategoryLinksHelper::maskParserFunctions()}.
	 *
	 * @param array $replacedQueries Array with information about replacements done.
	 * 	Got from {@link CategoryLinksHelper::maskParserFunctions()}
	 */
	public function unmaskParserFunctions( array $replacedQueries ) {
		if ( empty( $replacedQueries ) ) {
			return;
		}
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
	 * @return string
	 */
	public function getWikitext() {
		return $this->wikitext;
	}

	/**
	 *
	 * @param string $fullMatch
	 * @param string $leadingColon
	 * @param string $titleText
	 * @return Title|null
	 */
	protected function makeTitleFromMatch( $fullMatch, $leadingColon, $titleText ) {
		return Title::newFromText( $titleText );
	}

	/**
	 *
	 * @return string
	 */
	protected function getPattern() {
		return "#\[\[([ :])?(.*?)([\|].*?\]\]|\]\])#si";
	}

	/**
	 *
	 * @return array
	 */
	public function getTargets() {
		return $this->parse();
	}

	/**
	 *
	 * @param Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( Title $target, $removeAllOccurrences ) {
		foreach ( $this->getTargets() as $match => $title ) {
			if ( !$target->equals( $title ) ) {
				continue;
			}
			$this->wikitext = preg_replace(
				"#" . preg_quote( $match, '/' ) . "#si",
				'',
				$this->wikitext,
				$removeAllOccurrences ? -1 : 1
			);
			break;
		}
	}

	/**
	 *
	 * @param Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 * @param bool $leadingColon
	 * @param string $separator
	 */
	protected function addTarget( Title $target, $text, $addDuplicates, $leadingColon = true,
		$separator = "\n" ) {
		if ( !$addDuplicates ) {
			foreach ( $this->getTargets() as $match => $title ) {
				if ( !$target->equals( $title ) ) {
					continue;
				}
				return;
			}
		}
		$linkWikiText = "[[";
		if ( !empty( $this->wikitext ) ) {
			$linkWikiText = $separator . $linkWikiText;
		}

		if ( $target->getNamespace() !== NS_MAIN ) {
			if ( $leadingColon && in_array( $target->getNamespace(), [ NS_FILE, NS_CATEGORY ] ) ) {
				$linkWikiText .= ':';
			}
			$linkWikiText .= MediaWikiServices::getInstance()
				->getNamespaceInfo()
				->getCanonicalName( $target->getNamespace() );
			$linkWikiText .= ':';
		}
		$linkWikiText .= $target->getText();
		if ( $text ) {
			$linkWikiText .= "|$text";
		}
		$linkWikiText .= "]]";
		$this->wikitext .= $linkWikiText;
	}

	/**
	 *
	 * @param Title[] $links
	 * @param bool|false $removeAllOccurrences
	 * @return InternalLinksHelper
	 */
	public function removeTargets( $links, $removeAllOccurrences = false ) {
		$replaced = $this->maskParserFunctions();
		foreach ( $links as $linkText => $target ) {
			if ( !$target instanceof Title ) {
				continue;
			}
			$this->removeTarget( $target, $removeAllOccurrences );
		}
		$this->unmaskParserFunctions( $replaced );
		return $this;
	}

	/**
	 *
	 * @param Title[] $links
	 * @param bool|true $addDuplicates
	 * @param string $separator
	 * @return InternalLinksHelper
	 */
	public function addTargets( $links, $addDuplicates = true, $separator = "\n" ) {
		foreach ( $links as $linkText => $target ) {
			if ( !$target instanceof Title ) {
				continue;
			}
			if ( empty( $linkText ) || is_numeric( $linkText ) ) {
				$linkText = false;
			}
			$this->addTarget( $target, $linkText, $addDuplicates, true, $separator );
		}
		return $this;
	}
}
