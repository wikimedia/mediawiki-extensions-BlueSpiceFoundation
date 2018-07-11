<?php

/**
 * Hook handler base class for MediaWiki hook BaseTemplateToolbox
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
 * @author     Peter Boehm <boehm@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class BaseTemplateToolbox extends Hook {

	/**
	 *
	 * @var \BaseTemplate
	 */
	protected $baseTemplate = null;

	/**
	 * An array of toolbox items
	 * @var array
	 */
	protected $toolbox = [];

	/**
	 *
	 * @param \BaseTemplate $baseTemplate
	 * @param array $toolbox
	 * @return boolean
	 */
	public static function callback( $baseTemplate, &$toolbox ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$baseTemplate,
			$toolbox
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \BaseTemplate $baseTemplate
	 * @param array $toolbox
	 */
	public function __construct( $context, $config, $baseTemplate, &$toolbox ) {
		parent::__construct( $context, $config );

		$this->baseTemplate = $baseTemplate;
		$this->toolbox =& $toolbox;
	}

}
