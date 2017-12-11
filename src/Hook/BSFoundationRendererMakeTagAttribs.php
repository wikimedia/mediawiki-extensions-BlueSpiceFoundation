<?php
/**
 * Hook handler base class for BlueSpice hook BSFoundationRendererMakeTagAttribs
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
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSFoundationRendererMakeTagAttribs extends Hook {

	/**
	 * Instance of the renderer
	 * @var \BlueSpice\Renderer
	 */
	protected $renderer = null;

	/**
	 * Arguments the renderer was initialized
	 * @var array
	 */
	protected $args = null;

	/**
	 * Attributes that will be used to render the tag
	 * @var array
	 */
	protected $attribs = null;

	/**
	 * Located in \BlueSpice\Renderer::makeTagAttribs. Provides the tags
	 * attributes $attrbs as a reference. Use this hook to add or modify the
	 * tags attributes.
	 * @param \BlueSpice\Renderer $renderer
	 * @param array $args
	 * @param array $attribs
	 * @return type
	 */
	public static function callback( $renderer, $args, &$attribs ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$renderer,
			$args,
			$attribs
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BlueSpice\Renderer $renderer
	 * @param array $args
	 * @param array $attribs
	 */
	public function __construct( $context, $config, $renderer, $args, &$attribs ) {
		parent::__construct( $context, $config );

		$this->renderer = $renderer;
		$this->args = $args;
		$this->attribs = &$attribs;
	}
}
