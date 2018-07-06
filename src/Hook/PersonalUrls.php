<?php

/**
 * Hook handler base class for MediaWiki hook PersonalUrls
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

abstract class PersonalUrls extends Hook {

	/**
	 * the array of URLs set up so far
	 * @var array
	 */
	protected $personal_urls = [];

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @var \SkinTemplate
	 */
	protected $skin = null;

	/**
	 *
	 * @param array $personal_urls
	 * @param Title $title
	 * @param SkinTemplate $skin
	 * @return boolean
	 */
	public static function callback( &$personal_urls, $title, $skin ) {
		$className = static::class;

		$hookHandler = new $className(
			null,
			null,
			$personal_urls,
			$title,
			$skin
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param array $personal_urls
	 * @param Title $title
	 * @param SkinTemplate $skin
	 */
	public function __construct( $context, $config, &$personal_urls, $title, $skin ) {
		parent::__construct( $context, $config );
		$this->personal_urls =& $personal_urls;
		$this->title = $title;
		$this->skin = $skin;
	}

}
