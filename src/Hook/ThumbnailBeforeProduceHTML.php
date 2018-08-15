<?php
/**
 * Hook handler base class for MediaWiki hook ThumbnailBeforeProduceHTML
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
 * This thumbnail is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric WIrth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @thumbnailsource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class ThumbnailBeforeProduceHTML extends Hook {

	/**
	 *
	 * @var \ThumbnailImage
	 */
	protected $thumbnail = null;

	/**
	 *
	 * @var array
	 */
	protected $attribs = [];

	/**
	 *
	 * @var array
	 */
	protected $linkAttribs  = [];

	/**
	 * @param \ThumbnailImage $thumbnail
	 * @param array $attribs
	 * @param array $linkAttribs
	 * @return boolean Always true to keep hook running
	 */
	public static function callback( $thumbnail, &$attribs, &$linkAttribs  ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$thumbnail,
			$attribs,
			$linkAttribs
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \ThumbnailImage $thumbnail
	 * @param array $attribs
	 * @param array $linkAttribs
	 */
	public function __construct( $context, $config, $thumbnail, &$attribs, &$linkAttribs ) {
		parent::__construct( $context, $config );

		$this->thumbnail = $thumbnail;
		$this->attribs =& $attribs;
		$this->linkAttribs =& $linkAttribs;
	}
}
