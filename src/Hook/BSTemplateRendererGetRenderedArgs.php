<?php
/**
 * Hook handler base class for BlueSpice hook BSTemplateRendererGetRenderedArgs
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
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\TemplateRenderer;

abstract class BSTemplateRendererGetRenderedArgs extends \BlueSpice\Hook {
	/**
	 * The Renderer which is processing the arguments
	 * @var TemplateRenderer
	 */
	protected $renderer = null;

	/**
	 * Name of the argument that gets processed
	 * @var string
	 */
	protected $name = null;

	/**
	 * Value that gets processed
	 * @var mixed
	 */
	protected $val = null;

	/**
	 * Result of the processed value. Before this overwrites the value.
	 * @var string
	 */
	protected $renderedVal = null;

	/**
	 * Located in \BlueSpice\TemplateRenderer::getRenderedArgs. When all
	 * arguments for the mustache template get processed. Use to change the
	 * render output.
	 * @param TemplateRenderer $renderer
	 * @param string $name
	 * @param mixed $val
	 * @param string $renderedVal
	 * @return boolean
	 */
	public static function callback( $renderer, $name, $val, &$renderedVal ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$renderer,
			$name,
			$val,
			$renderedVal
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param TemplateRenderer $renderer
	 * @param string $name
	 * @param mixed $val
	 * @param string $renderedVal
	 */
	public function __construct( $context, $config, $renderer, $name, $val, &$renderedVal ) {
		parent::__construct( $context, $config );

		$this->renderer = $renderer;
		$this->name = $name;
		$this->val = $val;
		$this->renderedVal = &$renderedVal;
	}
}
