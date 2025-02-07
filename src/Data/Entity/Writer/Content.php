<?php

namespace BlueSpice\Data\Entity\Writer;

use BlueSpice\Data\Entity\Writer;
use BlueSpice\Entity;
use Exception;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Status\Status;

abstract class Content extends Writer {
	/**
	 * @param Entity $entity
	 * @return Status
	 */
	public function writeEntity( Entity $entity ) {
		$data = array_intersect_key(
			(array)$entity->getFullData(),
			array_flip( $this->getSchema()->getStorableFields() )
		);
		$contentClass = $entity->getConfig()->get( 'ContentClass' );
		if ( !class_exists( $contentClass ) ) {
			return Status::newFatal(
				"Content class '$contentClass' not found"
			);
		}
		if ( empty( $data['id'] ) ) {
			$data['id'] = $contentClass::generateID( $entity );
		}
		if ( empty( $data['id'] ) ) {
			return Status::newFatal( 'No ID generated' );
		}
		$entity->set( Entity::ATTR_ID, $data['id'] );
		$wikiPage = MediaWikiServices::getInstance()->getWikiPageFactory()->newFromTitle( $entity->getTitle() );
		$user = $this->context->getUser();
		$content = new $contentClass( FormatJson::encode( $data ) );
		$updater = $wikiPage->newPageUpdater( $user );
		$updater->setContent( SlotRecord::MAIN, $content );
		$comment = CommentStoreComment::newUnsavedComment( '' );
		try {
			$updater->saveRevision( $comment );
		} catch ( Exception $e ) {
			// Something probalby breaks json
			return Status::newFatal( $e->getMessage() );
		}
		return $updater->getStatus();
	}
}
