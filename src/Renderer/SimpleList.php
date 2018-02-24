<?php

namespace BlueSpice\Renderer;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Renderer\SimpleList\Item;

class SimpleList extends \BlueSpice\Renderer {
	const PARAM_ITEMS = 'items';

	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->args[static::PARAM_ITEMS] = $params->get(
			static::PARAM_ITEMS,
			[]
		);
		if( !in_array( $this->args[static::PARAM_TAG], ['ol', 'ul'] ) ) {
			$this->args[static::PARAM_TAG] = 'ul';
		}
	}

	public function render() {
		$content = '';
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();
		return $content;
	}

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
