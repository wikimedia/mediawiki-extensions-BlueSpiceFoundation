<?php
/**
 * Hook handler base class for MediaWiki hook ArticleDeleteComplete
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

abstract class ArticleDeleteComplete extends Hook {

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
	 * @var integer
	 */
	protected $id = null;

	/**
	 *
	 * @var \Content
	 */
	protected $content = null;

	/**
	 *
	 * @var \LogEntry
	 */
	protected $logEntry = false;

	/**
	 *
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param string $reason
	 * @param integer $id
	 * @param \Content $content
	 * @param \LogEntry $logEntry
	 * @return boolean
	 */
	public static function callback( &$wikipage, &$user, $reason, $id, $content, $logEntry ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$wikipage,
			$user,
			$reason,
			$id,
			$content,
			$logEntry
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param string $reason
	 * @param integer $id
	 * @param \Content $content
	 * @param \LogEntry $logEntry
	 */
	public function __construct( $context, $config, &$wikipage, &$user, $reason, $id, $content, $logEntry ) {
		parent::__construct( $context, $config );

		$this->wikipage = &$wikipage;
		$this->user = &$user;
		$this->reason = $reason;
		$this->id = $id;
		$this->content = $content;
		$this->logEntry = $logEntry;
	}
}
