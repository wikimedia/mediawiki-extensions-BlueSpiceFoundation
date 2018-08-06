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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;
use BlueSpice\Renderer\Params;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\MediaWikiServices;

abstract class Renderer implements IRenderer {
	const PARAM_ID = 'id';
	const PARAM_CLASS = 'class';
	const PARAM_TAG = 'tag';
	const PARAM_CONTENT = 'content';

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

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

	/**
	 * Constructor
	 * @param \Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 */
	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		$this->config = $config;
		$this->linkRenderer = $linkRenderer
			? $linkRenderer
			: MediaWikiServices::getInstance()->getLinkRenderer();

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
	}

	protected function makeTagContent() {
		$text = new \HtmlArmor( $this->args[static::PARAM_CONTENT] );
		return \HtmlArmor::getHtml( $text );
	}

	protected function getOpenTag() {
		return \Html::openElement(
			$this->args[static::PARAM_TAG],
			$this->makeTagAttribs()
		);
	}

	protected function makeTagAttribs() {
		$attrbs = [];
		if( $this->args[static::PARAM_CLASS] ) {
			$attrbs[static::PARAM_CLASS] = $this->args[static::PARAM_CLASS];
		}
		if( $this->args[static::PARAM_ID] ) {
			$attrbs[static::PARAM_ID] = $this->args[static::PARAM_ID];
		}
		\Hooks::run( 'BSFoundationRendererMakeTagAttribs', [
			$this,
			$this->args,
			&$attrbs
		]);
		return $attrbs;
	}

	protected function getCloseTag() {
		return \Html::closeElement( $this->args[static::PARAM_TAG] );
	}
}
