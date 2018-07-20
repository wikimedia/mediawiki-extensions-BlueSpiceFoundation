<?php
/**
 * Hook handler base class for MediaWiki hook FileDeleteComplete
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

abstract class FileDeleteComplete extends Hook {
	/**
	 *
	 * @var \File
	 */
	protected $file = null;
	/**
	 * Archive name
	 * @var string
	 */
	protected $oldimage = null;
	/**
	 *
	 * @var \WikiPage
	 */
	protected $wikipage = null;
	/**
	 *
	 * @var \User
	 */
	protected $user = null;
	/**
	 *
	 * @var string
	 */
	protected $reason = null;

	/**
	 *
	 * @param \File $file
	 * @param string $oldimage
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param string $reason
	 * @return boolean
	 */
	public static function callback( $file, $oldimage, $wikipage, $user, $reason ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$file,
			$oldimage,
			$wikipage,
			$user,
			$reason
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \File $file
	 * @param string $oldimage
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param string $reason
	 */
	public function __construct( $context, $config, $file, $oldimage, $wikipage, $user, $reason ) {
		parent::__construct( $context, $config );

		$this->file = $file;
		$this->oldimage = $oldimage;
		$this->wikipage = $wikipage;
		$this->user = $user;
		$this->reason = $reason;
	}
}
