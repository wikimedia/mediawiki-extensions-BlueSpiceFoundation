<?php

namespace BlueSpice\Task;

use Exception;
use Status;
use WikitextContent;

abstract class WikiPage extends \BlueSpice\Task {

	/**
	 *
	 * @param string $wikitext
	 * @return Status
	 */
	protected function saveWikiPage( $wikitext ) {
		$this->logger->debug( 'saveWikiPage', [ 'wikitext' => $wikitext ] );
		return $this->getWikiPage()->doEditContent(
			new WikitextContent( $wikitext ),
			$this->getSaveWikiPageSummary(),
			0,
			false,
			$this->getContext()->getUser()
		);
	}

	/**
	 * @return string
	 */
	abstract protected function getSaveWikiPageSummary();

	/**
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function fetchCurrentRevisionWikiText() {
		$content = $this->getWikiPage()->getContent();
		if( $content instanceof WikitextContent === false ) {
			throw new Exception(
				$this->msg( 'bs-wikipage-tasks-error-contentmodel' )->plain()
			);
		}
		$wikitext = $content->getNativeData();
		$this->logger->debug( 'fetchWikitext', [ 'wikitext' => $wikitext ] );
		return $wikitext;
	}

	/**
	 *
	 * @return \WikiPage
	 */
	protected function getWikiPage() {
		return $this->getContext()->getWikiPage();
	}

	/**
	 * @return boolean
	 */
	protected function shouldRunUpdates() {
		return true;
	}
}
