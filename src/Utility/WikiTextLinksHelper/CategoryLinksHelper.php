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
		if( !empty( $leadingColon ) ) {
			return null;
		}
		$title = parent::makeTitleFromMatch(
			$fullMatch,
			$leadingColon,
			$titleText
		);
		if( !$title ) {
			return $title;
		}
		if( $title->getNamespace() !== NS_CATEGORY ) {
			return null;
		}
		return $title;
	}

	/**
	 *
	 * @param \Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 */
	protected function addTarget( \Title $target, $text, $addDuplicates, $leadingColon = true, $separator = "\n" ) {
		if( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}

		return parent::addTarget( $target, false, false, false, "\n" );
	}

	/**
	 *
	 * @param \Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( \Title $target, $removeAllOccurrences ) {
		if( $target->getNamespace() !== NS_CATEGORY ) {
			return;
		}
		return parent::removeTarget( $target, $removeAllOccurrences );
	}
}
