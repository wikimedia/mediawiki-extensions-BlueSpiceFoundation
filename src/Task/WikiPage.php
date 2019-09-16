<?php

namespace BlueSpice\Task;

use BlueSpice\Task;
use Exception;
use Status;
use WikitextContent;
use MWException;

abstract class WikiPage extends Task {

	/**
	 * @param string $wikitext
	 * @return Status
	 * @throws MWException
	 */
	protected function saveWikiPage( $wikitext = '' ) {
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
		if ( $this->getWikiPage()->getTitle()->exists() === false ) {
			return '';
		}

		$content = $this->getWikiPage()->getContent();
		if ( $content instanceof WikitextContent === false ) {
			throw new Exception(
				$this->msg( 'bs-wikipage-tasks-error-contentmodel' )->plain()
			);
		}
		$wikitext = $content->getNativeData();
		$this->logger->debug( 'fetchWikitext', [ 'wikitext' => $wikitext ] );
		return $wikitext;
	}

	/**
	 * @return \WikiPage
	 */
	protected function getWikiPage() {
		return $this->getContext()->getWikiPage();
	}

	/**
	 * @return bool
	 */
	protected function shouldRunUpdates() {
		return true;
	}
}
