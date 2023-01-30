<?php
/**
 * Hook handler base class for MediaWiki hook PageSaveComplete
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
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\User\UserIdentity;
use WikiPage;

abstract class PageSaveComplete extends Hook {

	/**
	 *
	 * @var WikiPage
	 */
	protected $wikiPage = null;

	/**
	 *
	 * @var UserIdentity
	 */
	protected $user = null;

	/**
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 *
	 * @var int
	 */
	protected $flags = 0;

	/**
	 *
	 * @var RevisionRecord
	 */
	protected $revisionRecord = null;

	/**
	 *
	 * @var EditResult
	 */
	protected $editResult;

	/**
	 *
	 * @param WikiPage $wikiPage
	 * @param UserIdentity $user
	 * @param string $summary
	 * @param int $flags
	 * @param RevisionRecord $revisionRecord
	 * @param EditResult $editResult
	 * @return bool
	 */
	public static function callback( WikiPage $wikiPage, UserIdentity $user, string $summary, int $flags,
		RevisionRecord $revisionRecord, EditResult $editResult ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$wikiPage,
			$user,
			$summary,
			$flags,
			$revisionRecord,
			$editResult
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param type $context
	 * @param type $config
	 * @param WikiPage $wikiPage
	 * @param UserIdentity $user
	 * @param string $summary
	 * @param int $flags
	 * @param RevisionRecord $revisionRecord
	 * @param EditResult $editResult
	 */
	public function __construct( $context, $config, WikiPage $wikiPage, UserIdentity $user, string $summary, int $flags,
		RevisionRecord $revisionRecord, EditResult $editResult ) {
		parent::__construct( $context, $config );

		$this->wikiPage = $wikiPage;
		$this->user = $user;
		$this->summary = $summary;
		$this->flags = $flags;
		$this->revisionRecord = $revisionRecord;
		$this->editResult = $editResult;
	}
}
