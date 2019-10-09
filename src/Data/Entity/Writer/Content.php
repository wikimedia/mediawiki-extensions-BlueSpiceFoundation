<?php

namespace BlueSpice\Data\Entity\Writer;

use Exception;
use FormatJson;
use WikiPage;
use Status;
use BlueSpice\Data\Entity\Writer;
use BlueSpice\Entity;

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
		$wikiPage = WikiPage::factory( $entity->getTitle() );
		try {
			$status = $wikiPage->doEditContent(
				new $contentClass( FormatJson::encode( $data ) ),
				"",
				0,
				0,
				$this->context->getUser(),
				null
			);
		} catch ( Exception $e ) {
			// Something probalby breaks json
			return Status::newFatal( $e->getMessage() );
		}
		return $status;
	}
}
