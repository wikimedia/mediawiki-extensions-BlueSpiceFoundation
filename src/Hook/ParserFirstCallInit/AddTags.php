<?php

namespace BlueSpice\Hook\ParserFirstCallInit;

use BlueSpice\Hook\ParserFirstCallInit;
use BlueSpice\Tag\GenericHandler;

class AddTags extends ParserFirstCallInit {

	protected function doProcess() {
		$factory = $this->getServices()->getBSTagFactory();
		$tags = $factory->getAll();
		foreach( $tags as $tag ) {
			$genericHandler = new GenericHandler( $tag );
			$tagNames = $tag->getTagNames();
			foreach( $tagNames as $tagName ) {
				$this->parser->setHook( $tagName, [ $genericHandler, 'handle' ] );
			}
		}

		return true;
	}
}
