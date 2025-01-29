<?php

/**
 * Hook handler base class for MediaWiki hook BeforeInitialize
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2018 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Output\OutputPage;
use MediaWiki\Request\WebRequest;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

abstract class BeforeInitialize extends Hook {

	/**
	 * @var Title
	 */
	protected $title;

	/**
	 * Should be unused and always null
	 *
	 * @var \Article|null
	 */
	protected $article;

	/**
	 * @var OutputPage
	 */
	protected $output;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var WebRequest
	 */
	protected $request;

	/**
	 * @var \MediaWiki
	 */
	protected $mediaWiki;

	/**
	 * @param Title &$title
	 * @param \Article|null &$article
	 * @param OutputPage &$output
	 * @param User &$user
	 * @param WebRequest $request
	 * @param \MediaWiki $mediaWiki
	 * @return bool
	 */
	public static function callback( &$title, &$article, &$output, &$user, $request, $mediaWiki ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$article,
			$output,
			$user,
			$request,
			$mediaWiki
		);
		return $hookHandler->process();
	}

	/**
	 * @param \ContextSource $context
	 * @param Config $config
	 * @param Title &$title
	 * @param \Article|null &$article
	 * @param OutputPage &$output
	 * @param User &$user
	 * @param WebRequest $request
	 * @param \MediaWiki $mediaWiki
	 */
	public function __construct( $context, $config, &$title, &$article, &$output, &$user, $request,
		$mediaWiki ) {
		parent::__construct( $context, $config );

		$this->title =& $title;
		$this->article =& $article;
		$this->output =& $output;
		$this->user =& $user;
		$this->request = $request;
		$this->mediaWiki = $mediaWiki;
	}

}
