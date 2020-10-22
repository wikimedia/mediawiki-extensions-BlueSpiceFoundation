<?php

namespace BlueSpice\Data\Entity\Reader;

use BlueSpice\Content\Entity as EntityContent;
use BlueSpice\EntityConfig;
use Title;
use WikiPage;

abstract class Content extends \BlueSpice\Data\Entity\Reader {
	/**
	 *
	 * @param mixed $id
	 * @param EntityConfig $entityConfig
	 * @return \stdClass|null
	 */
	public function resolveNativeDataFromID( $id, EntityConfig $entityConfig ) {
		$entityClass = $entityConfig->get( 'EntityClass' );
		if ( !class_exists( $entityClass ) ) {
			return null;
		}
		$title = Title::makeTitle( $entityClass::NS, $id );

		if ( !$title || !$title->exists() ) {
			return null;
		}

		$content = WikiPage::factory( $title )->getContent();
		if ( !$content ) {
			return null;
		}
		$text = $content->getNativeData();

		$content = new EntityContent( $text );
		$data = (object)$content->getData()->getValue();

		return $data;
	}
}
