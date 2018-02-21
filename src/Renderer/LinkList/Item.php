<?php

namespace BlueSpice\Renderer\LinkList;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use BlueSpice\Renderer\Params;

class Item extends \BlueSpice\Renderer\SimpleList\Item {
	const PARAM_TARGET = 'target';

	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->args[static::PARAM_TARGET] = $params->get(
			static::PARAM_TARGET,
			false
		);
	}

	protected function makeTagContent() {
		$content = '';
		$text = new \HtmlArmor( $this->args[static::PARAM_TEXT] );
		if( $this->args[static::PARAM_TARGET] instanceof LinkTarget ) {
			$content .= $this->linkRenderer->makeLink(
				$this->args[static::PARAM_TARGET],
				$text
			);
		} else {
			$content .= \HtmlArmor::getHtml( $text );
		}
		return $content;
	}
}
