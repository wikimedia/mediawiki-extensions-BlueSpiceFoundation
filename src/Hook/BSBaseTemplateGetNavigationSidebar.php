<?php
/**
 * Hook handler base class for BlueSpice hook BSBaseTemplateGetNavigationSidebar
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class BSBaseTemplateGetNavigationSidebar extends Hook {
	/**
	 *
	 * @var \BsBaseTemplate
	 */
	protected $template = null;

	/**
	 * An array of \ViewBaseElement's
	 * @var \ViewBaseElement[]
	 */
	protected $portlets = null;

	/**
	 * An array of html strings. All executed \ViewBaseElement's output will be
	 * stored there.
	 * @var array
	 */
	protected $outputItems = null;

	/**
	 * Located in \BsBaseTemplate::getNavigationSidebar. Before the
	 * \ViewBaseElement's in $portlets are executed and their results stored in
	 * $outputItems
	 * @param \BsBaseTemplate $template
	 * @param array $portlets
	 * @param array $outputItems
	 * @return type
	 */
	public static function callback( $template, &$portlets, &$outputItems ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$template,
			$portlets,
			$outputItems
		);
		return $hookHandler->process();
	}

	/**
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BsBaseTemplate $template
	 * @param array $portlets
	 * @param array $outputItems
	 */
	public function __construct( $context, $config, $template, &$portlets, &$outputItems ) {
		parent::__construct( $context, $config );

		$this->template = $template;
		$this->portlets = &$portlets;
		$this->outputItems = &$outputItems;
	}
}