<?php
/**
 * Hook handler base class for MediaWiki hook LinkerMakeExternalLink
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
 * @author     Patric WIrth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class LinkerMakeExternalLink extends Hook {

	/**
	 * @var string
	 */
	protected $url = '';

	/**
	 * @var string
	 */
	protected $text = '';

	/**
	 * @var string
	 */
	protected $link = '';

	/**
	 * @var array
	 */
	protected $attribs = [];

	/**
	 * @var string
	 */
	protected $linktype = '';

	/**
	 *
	 * @param string &$url
	 * @param string &$text
	 * @param string &$link
	 * @param array &$attribs
	 * @param string $linktype
	 * @return bool
	 */
	public static function callback( &$url, &$text, &$link, &$attribs, $linktype ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$url,
			$text,
			$link,
			$attribs,
			$linktype
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string &$url
	 * @param string &$text
	 * @param string &$link
	 * @param array &$attribs
	 * @param string $linktype
	 * @return bool
	 */
	public function __construct( $context, $config, &$url, &$text, &$link, &$attribs, $linktype ) {
		parent::__construct( $context, $config );

		$this->url =& $url;
		$this->text =& $text;
		$this->link =& $link;
		$this->attribs =& $attribs;
		$this->linktype = $linktype;
	}
}
