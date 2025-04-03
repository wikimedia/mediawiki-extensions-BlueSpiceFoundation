<?php

namespace BlueSpice\Renderer\LinkList;

use BlueSpice\Renderer\Params;
use HtmlArmor;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;

class Item extends \BlueSpice\Renderer\SimpleList\Item {
	public const PARAM_TARGET = 'target';

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 */
	protected function __construct( Config $config, Params $params,
		?LinkRenderer $linkRenderer = null, ?IContextSource $context = null,
		$name = '' ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );
		$this->args[static::PARAM_TARGET] = $params->get(
			static::PARAM_TARGET,
			false
		);
	}

	protected function makeTagContent() {
		$content = '';
		$text = new HtmlArmor( $this->args[static::PARAM_TEXT] );
		if ( $this->args[static::PARAM_TARGET] instanceof LinkTarget ) {
			$content .= $this->linkRenderer->makeLink(
				$this->args[static::PARAM_TARGET],
				$text
			);
		} else {
			$content .= HtmlArmor::getHtml( $text );
		}
		return $content;
	}
}
