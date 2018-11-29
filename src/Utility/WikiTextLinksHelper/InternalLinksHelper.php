<?php

namespace BlueSpice\Utility\WikiTextLinksHelper;

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
		$pattern = $this->getPattern();
		$matches = [];
		$matchCount = preg_match_all(
			$pattern,
			$this->wikitext,
			$matches,
			PREG_SET_ORDER
		);
		$links = [];
		foreach( $matches as $match ) {
			list( $fullMatch, $leadingColon, $titleText ) = $match;
			$title = $this->makeTitleFromMatch(
				$fullMatch,
				$leadingColon,
				$titleText
			);
			if( !$title ) {
				continue;
			}
			$links[$fullMatch] = $title;
		}
		return $links;
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
	 * @return \Title|null
	 */
	protected function makeTitleFromMatch( $fullMatch, $leadingColon, $titleText ) {
		return \Title::newFromText( $titleText );
	}

	/**
	 *
	 * @return string
	 */
	protected function getPattern() {
		return  "#\[\[([ :])?(.*?)([\|].*?\]\]|\]\])#si";
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
	 * @param \Title $target
	 * @param bool $removeAllOccurrences
	 */
	protected function removeTarget( \Title $target, $removeAllOccurrences ) {
		foreach( $this->getTargets() as $match => $title ) {
			if( !$target->equals( $title ) ) {
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
	 * @param \Title $target
	 * @param string|false $text
	 * @param bool $addDuplicates
	 */
	protected function addTarget( \Title $target, $text, $addDuplicates, $leadingColon = true, $separator = "\n" ) {
		if( !$addDuplicates ) {
			foreach( $this->getTargets() as $match => $title ) {
				if( !$target->equals( $title ) ) {
					continue;
				}
				return;
			}
		}
		$linkWikiText = "[[";
		if( !empty( $this->wikitext ) ) {
			$linkWikiText = $separator . $linkWikiText;
		}

		if( $target->getNamespace() !== NS_MAIN ) {
			if( $leadingColon && in_array( $target->getNamespace() , [NS_FILE, NS_CATEGORY] ) ) {
				$linkWikiText .= ':';
			}
			$linkWikiText .= \MWNamespace::getCanonicalName(
				$target->getNamespace()
			);
			$linkWikiText .= ':';
		}
		$linkWikiText .= $target->getText();
		if( $text ) {
			$linkWikiText .= "|$text";
		}
		$linkWikiText .= "]]";
		$this->wikitext .= $linkWikiText;
	}

	/**
	 *
	 * @param \Title[] $links
	 * @param bool|false $removeAllOccurrences
	 * @return InternalLinksHelper
	 */
	public function removeTargets( $links, $removeAllOccurrences = false ) {
		foreach( $links as $linkText => $target ) {
			if( !$target instanceof \Title ) {
				continue;
			}
			$this->removeTarget( $target, $removeAllOccurrences );
		}
		return $this;
	}

	/**
	 *
	 * @param \Title[] $links
	 * @param bool|true $addDuplicates
	 * @return InternalLinksHelper
	 */
	public function addTargets( $links, $addDuplicates = true, $separator = "\n" ) {
		foreach( $links as $linkText => $target ) {
			if( !$target instanceof \Title ) {
				continue;
			}
			if( empty( $linkText ) || is_numeric( $linkText ) ) {
				$linkText = false;
			}
			$this->addTarget( $target, $linkText, $addDuplicates, true, $separator );
		}
		return $this;
	}
}
