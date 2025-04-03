<?php
/**
 * Renderer base class for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use BlueSpice\Renderer\Params;
use HtmlArmor;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\Html\Html;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MessageLocalizer;

abstract class Renderer implements IRenderer, MessageLocalizer {
	public const PARAM_CONTEXT = 'context';

	public const PARAM_ID = 'id';
	public const PARAM_CLASS = 'class';
	public const PARAM_TAG = 'tag';
	public const PARAM_CONTENT = 'content';

	/**
	 * Name of the Renderer
	 * @var string
	 */
	protected $name = '';

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var LinkRenderer
	 */
	protected $linkRenderer = null;

	/**
	 *
	 * @var array
	 */
	protected $args = [];

	/** @var MediaWikiServices */
	protected $services = null;

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
		$this->config = $config;
		$this->context = $context;
		$this->linkRenderer = $linkRenderer;
		$this->name = $name;

		$this->args[static::PARAM_CLASS] = $params->get(
			static::PARAM_CLASS,
			false
		);
		$this->args[static::PARAM_ID] = $params->get(
			static::PARAM_ID,
			false
		);
		$this->args[static::PARAM_TAG] = $params->get(
			static::PARAM_TAG,
			'div'
		);
		$this->args[static::PARAM_CONTENT] = $params->get(
			static::PARAM_CONTENT,
			''
		);
		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 *
	 * @param string $name
	 * @param MediaWikiServices $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @return Renderer
	 */
	public static function factory( $name, MediaWikiServices $services, Config $config,
		Params $params, ?IContextSource $context = null, ?LinkRenderer $linkRenderer = null ) {
		if ( !$context ) {
			$context = $params->get(
				static::PARAM_CONTEXT,
				false
			);
			if ( !$context instanceof IContextSource ) {
				$context = RequestContext::getMain();
			}
		}
		if ( !$linkRenderer ) {
			$linkRenderer = $services->getLinkRenderer();
		}

		return new static( $config, $params, $linkRenderer, $context, $name );
	}

	/**
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params Normal message parameters
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		return $this->getContext()->msg( $key, ...$params );
	}

	/**
	 *
	 * @return IContextSource
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 *
	 * @return string
	 */
	protected function makeTagContent() {
		$text = new HtmlArmor( $this->args[static::PARAM_CONTENT] );
		return HtmlArmor::getHtml( $text );
	}

	/**
	 *
	 * @return array
	 */
	protected function getOpenTag() {
		return Html::openElement(
			$this->args[static::PARAM_TAG],
			$this->makeTagAttribs()
		);
	}

	/**
	 *
	 * @return array
	 */
	protected function makeTagAttribs() {
		$attrbs = [];
		if ( $this->args[static::PARAM_CLASS] ) {
			$attrbs[static::PARAM_CLASS] = $this->args[static::PARAM_CLASS];
		}
		if ( $this->args[static::PARAM_ID] ) {
			$attrbs[static::PARAM_ID] = $this->args[static::PARAM_ID];
		}
		$this->services->getHookContainer()->run(
			'BSFoundationRendererMakeTagAttribs',
			[
				$this,
				$this->args,
				&$attrbs
			]
		);
		return $attrbs;
	}

	/**
	 *
	 * @return string
	 */
	protected function getCloseTag() {
		return Html::closeElement( $this->args[static::PARAM_TAG] );
	}

	/**
	 * Name of the Renderer
	 * @return string
	 */
	protected function getName() {
		return $this->name;
	}
}
