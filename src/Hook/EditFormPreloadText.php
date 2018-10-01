<?php
/**
 * Hook handler base class for MediaWiki hook EditFormPreloadText
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class EditFormPreloadText extends Hook {

	/**
	 *
	 * @var string
	 */
	protected $text = '';

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @param string $text
	 * @param \Title $title
	 * @return boolean
	 */
	public static function callback( &$text, &$title ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$text,
			$title
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param string $text
	 * @param \Title $title
	 */
	public function __construct( $context, $config, &$text, &$title ) {
		parent::__construct( $context, $config );

		$this->text =& $text;
		$this->title =& $title;
	}
}
