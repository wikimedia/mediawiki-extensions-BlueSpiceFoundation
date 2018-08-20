<?php
/**
 * Hook handler base class for MediaWiki hook LinkerMakeMediaLinkFile
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
 * @author     Patric WIrth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class LinkerMakeMediaLinkFile extends Hook {

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @var \File
	 */
	protected $file = null;

	/**
	 *
	 * @var string
	 */
	protected $html = '';

	/**
	 *
	 * @var array
	 */
	protected $attribs = [];

	/**
	 *
	 * @var string
	 */
	protected $ret  = '';

	/**
	 * @param \Title $title
	 * @param \File $file
	 * @param string $html
	 * @param array $attribs
	 * @param string $ret
	 * @return boolean Always true to keep hook running
	 */
	public static function callback( \Title $title, $file, &$html, &$attribs, &$ret  ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$file,
			$html,
			$attribs,
			$ret
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \Title $title
	 * @param \File $file
	 * @param string $html
	 * @param array $attribs
	 * @param string $ret
	 */
	public function __construct( $context, $config, \Title $title, $file, &$html, &$attribs, &$ret ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->file = $file;
		$this->html =& $html;
		$this->attribs =& $attribs;
		$this->ret =& $ret;
	}
}
