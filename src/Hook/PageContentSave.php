<?php
/**
 * Hook handler base class for MediaWiki hook PageContentSave
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
 * @author     Peter Boehm <boehm@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Content\Content;
use MediaWiki\Context\IContextSource;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Status\Status;
use MediaWiki\User\User;

abstract class PageContentSave extends Hook {

	/**
	 *
	 * @var \WikiPage
	 */
	protected $wikipage = null;

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var Content
	 */
	protected $content = null;

	/**
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 *
	 * @var bool
	 */
	protected $isMinor = false;

	/**
	 *
	 * @var bool
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
	 * @var RevisionRecord
	 */
	protected $revision = null;

	/**
	 *
	 * @var Status
	 */
	protected $status = null;

	/**
	 *
	 * @var int
	 */
	protected $baseRevId = 0;

	/**
	 *
	 * @param \WikiPage &$wikipage
	 * @param User &$user
	 * @param Content &$content
	 * @param string &$summary
	 * @param bool $isMinor
	 * @param bool $isWatch
	 * @param int $section
	 * @param int &$flags
	 * @param Status &$status
	 * @return bool
	 */
	public static function callback( &$wikipage, &$user, &$content, &$summary, $isMinor, $isWatch,
		$section, &$flags, &$status ) {
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
			$status
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param \WikiPage &$wikipage
	 * @param User &$user
	 * @param Content &$content
	 * @param string &$summary
	 * @param bool $isMinor
	 * @param bool $isWatch
	 * @param int $section
	 * @param int &$flags
	 * @param Status &$status
	 */
	public function __construct( $context, $config, &$wikipage, &$user, &$content, &$summary, $isMinor,
		$isWatch, $section, &$flags, &$status ) {
		parent::__construct( $context, $config );

		$this->wikipage =& $wikipage;
		$this->user =& $user;
		$this->content =& $content;
		$this->summary =& $summary;
		$this->isMinor = $isMinor;
		$this->section = $section;
		$this->flags =& $flags;
		$this->status =& $status;
	}
}
