<?php

namespace BlueSpice\Renderer\SimpleList;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;

class Item extends \BlueSpice\Renderer {

	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->args[static::PARAM_TAG] = 'li';
	}

	public function render() {
		$content = '';
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();
		return $content;
	}
}
