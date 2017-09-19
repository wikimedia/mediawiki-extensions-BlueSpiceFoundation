<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

use BlueSpice\Services;

class InterwikiLinksHelper extends InternalLinksHelper {

	/**
	 *
	 * @var Services
	 */
	protected $services = null;

	/**
	 *
	 * @param string &$wikitext
	 */
	public function __construct( &$wikitext, Services $services ) {
		parent::__construct( $wikitext );
		$this->services = $services;
	}

	/**
	 *
	 * @param \Title|null $title
	 * @return bool
	 */
	protected function isValidInterwikiLink( \Title $title = null ) {
		return !empty( $title->getInterwiki() );
	}

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

		return $this->isValidInterwikiLink( $title ) ? $title : null;
	}

	/**
	 *
	 * @param \Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 */
	protected function addTarget( \Title $target, $text, $addDuplicates, $leadingColon = true, $separator = "\n" ) {
		if ( !$this->isValidInterwikiLink( $target ) ) {
			return;
		}

		foreach( $this->getTargets() as $match => $title ) {
			if( !$target->equals( $title ) ) {
				continue;
			}
			return;
		}

		$linkWikiText = "[[";
		if( !empty( $this->wikitext ) ) {
			$linkWikiText = $separator . $linkWikiText;
		}
		$linkWikiText .= $target->getPrefixedText();
		$linkWikiText .= "]]";
		$this->wikitext .= $linkWikiText;
	}

	/**
	 *
	 * @param \Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( \Title $target, $removeAllOccurrences ) {
		if ( !$this->isValidInterwikiLink( $target ) ) {
			return;
		}

		return parent::removeTarget( $target, $removeAllOccurrences );
	}

}
