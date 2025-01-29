<?php

namespace BlueSpice\Data\Entity\Reader;

use BlueSpice\Content\Entity as EntityContent;
use BlueSpice\Data\Entity\Reader;
use BlueSpice\EntityConfig;
use MediaWiki\Content\TextContent;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

abstract class Content extends Reader {

	/**
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

		$content = MediaWikiServices::getInstance()->getWikiPageFactory()->newFromTitle( $title )->getContent();
		if ( !$content ) {
			return null;
		}
		$text = ( $content instanceof TextContent ) ? $content->getText() : '';

		$content = new EntityContent( $text );
		$data = (object)$content->getData()->getValue();

		return $data;
	}
}
