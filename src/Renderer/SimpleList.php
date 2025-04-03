<?php

namespace BlueSpice\Renderer;

use BlueSpice\Renderer\SimpleList\Item;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Linker\LinkRenderer;

class SimpleList extends \BlueSpice\Renderer {
	public const PARAM_ITEMS = 'items';

	/**
	 *
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 */
	public function __construct( Config $config, Params $params,
		?LinkRenderer $linkRenderer = null, ?IContextSource $context = null,
		$name = '' ) {
		parent::__construct( $config, $params, $linkRenderer, $context );
		$this->args[static::PARAM_ITEMS] = $params->get(
			static::PARAM_ITEMS,
			[]
		);
		if ( !in_array( $this->args[static::PARAM_TAG], [ 'ol', 'ul' ] ) ) {
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
		foreach ( $this->args[static::PARAM_ITEMS] as $item ) {
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
