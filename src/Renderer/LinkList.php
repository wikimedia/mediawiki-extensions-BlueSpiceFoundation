<?php

namespace BlueSpice\Renderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Renderer\LinkList\Item;

class LinkList extends SimpleList {

	protected function makeTagContent() {
		$content = '';
		foreach( $this->args[static::PARAM_ITEMS] as $item ) {
			$renderer = new Item(
				$this->config,
				new Params( $item ),
				$this->linkRenderer
			);
			$content .= $renderer->render();
		}
		return $content;
	}
}
