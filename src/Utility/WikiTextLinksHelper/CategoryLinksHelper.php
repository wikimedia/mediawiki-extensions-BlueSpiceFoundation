<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

use BsNamespaceHelper;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

class CategoryLinksHelper extends InternalLinksHelper {

	/**
	 *
	 * @param string $fullMatch
	 * @param string $leadingColon
	 * @param string $titleText
	 * @return Title|null
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
	 * @param Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 * @param bool $leadingColon
	 * @param string $separator
	 */
	protected function addTarget( Title $target, $text, $addDuplicates, $leadingColon = true,
		$separator = "\n" ) {
		if ( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		parent::addTarget( $target, false, false, false, "\n" );
	}

	/**
	 * Gets all explicit categories, which exist in {@link CategoryLinksHelper::$wikitext}.
	 *
	 * @return string[] List of explicit category titles
	 */
	public function getExplicitCategories(): array {
		// Mask parser functions with category parameters to exclude them from search results
		$replaced = $this->maskParserFunctions();

		// Pattern for Category tags
		$canonicalNSName = MediaWikiServices::getInstance()
			->getNamespaceInfo()
			->getCanonicalName( NS_CATEGORY );
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

		$this->unmaskParserFunctions( $replaced );
		return $categories;
	}

	/**
	 *
	 * @param Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( Title $target, $removeAllOccurrences ) {
		if ( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		parent::removeTarget( $target, $removeAllOccurrences );
	}
}
