<?php

namespace BlueSpice\Task;

use BlueSpice\Task;
use Exception;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\TextContent;
use MediaWiki\Content\WikitextContent;
use MediaWiki\Context\RequestContext;
use MediaWiki\Deferred\DeferredUpdates;
use MediaWiki\EditPage\EditPage;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Status\Status;
use MWException;

abstract class WikiPage extends Task {

	/**
	 * @param string $wikitext
	 * @return Status
	 * @throws MWException
	 */
	protected function saveWikiPage( $wikitext = '' ) {
		$this->logger->debug( 'saveWikiPage', [ 'wikitext' => $wikitext ] );
		$user = $this->getContext()->getUser();
		$content = new WikitextContent( $wikitext );
		$updater = $this->getWikiPage()->newPageUpdater( $user );
		$updater->setContent( SlotRecord::MAIN, $content );
		$comment = CommentStoreComment::newUnsavedComment( $this->getSaveWikiPageSummary() );
		try {
			$updater->saveRevision( $comment );
		} catch ( Exception $e ) {
			return Status::newFatal( $e->getMessage() );
		}
		$status = $updater->getStatus();
		if ( $status->isGood() ) {
			$statusValue = $status->getValue();
			if ( !is_array( $statusValue ) || !isset( $statusValue['revision-record'] ) ) {
				return $status;
			}
			/** @var RevisionRecord $revision */
			$revision = $status->getValue()['revision-record'];
			$cookieName = EditPage::POST_EDIT_COOKIE_KEY_PREFIX . $revision->getId();
			// Must use main, since $this->getContext() returns different context
			$response = RequestContext::getMain()->getRequest()->response();
			DeferredUpdates::addCallableUpdate(
				static function () use ( $cookieName, $response ) {
					$response->clearCookie( $cookieName );
				}
			);
		}
		return $status;
	}

	/**
	 * @return string
	 */
	abstract protected function getSaveWikiPageSummary();

	/**
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
		$wikitext = ( $content instanceof TextContent ) ? $content->getText() : '';
		$this->logger->debug( 'fetchWikitext', [ 'wikitext' => $wikitext ] );
		return $wikitext;
	}

	/**
	 * @return \WikiPage
	 */
	protected function getWikiPage() {
		$services = MediaWikiServices::getInstance();
		return $services->getWikiPageFactory()->newFromTitle( $this->getContext()->getTitle() );
	}

	/**
	 * @return bool
	 */
	protected function shouldRunUpdates() {
		return true;
	}
}
