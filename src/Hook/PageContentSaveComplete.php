<?php
/**
 * Hook handler base class for MediaWiki hook PageContentSaveComplete
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
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class PageContentSaveComplete extends Hook {

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
	 * @var \Content
	 */
	protected $content = null;

	/**
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 *
	 * @var boolean
	 */
	protected $isMinor = false;

	/**
	 *
	 * @var boolean
	 */
	protected $isWatch = false;

	/**
	 *
	 * @var int
	 */
	protected $section = 0;

	/**
	 *
	 * @var int
	 */
	protected $flags = 0;

	/**
	 *
	 * @var \Revision
	 */
	protected $revision = null;

	/**
	 *
	 * @var \Status
	 */
	protected $status = null;

	/**
	 *
	 * @var int
	 */
	protected $baseRevId = 0;

	/**
	 *
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param \Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param int $section
	 * @param int $flags
	 * @param \Revision $revision
	 * @param \Status $status
	 * @param int $baseRevId
	 * @return boolean
	 */
	public static function callback( &$wikipage, &$user, $content, $summary, $isMinor, $isWatch, $section, &$flags, $revision, &$status, $baseRevId ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$wikipage,
			$user,
			$content,
			$summary,
			$isMinor,
			$isWatch,
			$section,
			$flags,
			$revision,
			$status,
			$baseRevId
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param \Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param int $section
	 * @param int $flags
	 * @param \Revision $revision
	 * @param \Status $status
	 * @param int $baseRevId
	 */
	public function __construct( $context, $config, &$wikipage, &$user, $content, $summary, $isMinor, $isWatch, $section, &$flags, $revision, &$status, $baseRevId ) {
		parent::__construct( $context, $config );

		$this->wikipage =& $wikipage;
		$this->user =& $user;
		$this->content = $content;
		$this->summary = $summary;
		$this->isMinor = $isMinor;
		$this->section = $section;
		$this->flags =& $flags;
		$this->revision = $revision;
		$this->status =& $status;
		$this->baseRevId = $baseRevId;
	}
}
