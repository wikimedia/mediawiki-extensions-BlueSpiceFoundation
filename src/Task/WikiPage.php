<?php

namespace BlueSpice\Task;

use BlueSpice\Task;
use DeferredUpdates;
use EditPage;
use Exception;
use MWCallableUpdate;
use MWException;
use RequestContext;
use Revision;
use Status;
use WikitextContent;

abstract class WikiPage extends Task {

	/**
	 * @param string $wikitext
	 * @return Status
	 * @throws MWException
	 */
	protected function saveWikiPage( $wikitext = '' ) {
		$this->logger->debug( 'saveWikiPage', [ 'wikitext' => $wikitext ] );
		$status = $this->getWikiPage()->doEditContent(
			new WikitextContent( $wikitext ),
			$this->getSaveWikiPageSummary(),
			0,
			false,
			$this->getContext()->getUser()
		);
		if ( $status->isGood() ) {
			$statusValue = $status->getValue();
			if ( !is_array( $statusValue ) || !isset( $statusValue['revision'] ) ) {
				return $status;
			}
			/** @var Revision $revision */
			$revision = $status->getValue()['revision'];
			$cookieName = EditPage::POST_EDIT_COOKIE_KEY_PREFIX . $revision->getId();
			// Must use main, since $this->getContext() returns different context
			$response = RequestContext::getMain()->getRequest()->response();
			DeferredUpdates::addUpdate(
				new MWCallableUpdate(
					function () use (
						$cookieName, $response
					) {
						$response->clearCookie( $cookieName );
					}
				)
			);

		}
		return $status;
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
