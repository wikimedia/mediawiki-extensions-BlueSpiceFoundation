<?php
/**
 * Hook handler base class for MediaWiki hook InitializeArticleMaybeRedirect
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
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Request\WebRequest;
use MediaWiki\Title\Title;
use WikiPage;

abstract class InitializeArticleMaybeRedirect extends Hook {

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var WebRequest
	 */
	protected $request = null;

	/**
	 *
	 * @var bool
	 */
	protected $ignoreRedirect = null;

	/**
	 *
	 * @var Title|string
	 */
	protected $target = '';

	/**
	 *
	 * @var WikiPage
	 */
	protected $article = null;

	/**
	 *
	 * @param Title $title
	 * @param WebRequest $request
	 * @param bool &$ignoreRedirect
	 * @param string &$target
	 * @param WikiPage $article
	 * @return bool
	 */
	public static function callback( $title, $request, &$ignoreRedirect, &$target, $article ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$request,
			$ignoreRedirect,
			$target,
			$article
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title $title
	 * @param WebRequest $request
	 * @param bool &$ignoreRedirect
	 * @param string &$target
	 * @param WikiPage $article
	 */
	public function __construct( $context, $config, $title, $request, &$ignoreRedirect,
		&$target, $article ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		$this->request = $request;
		$this->ignoreRedirect =& $ignoreRedirect;
		$this->target =& $target;
		$this->article = $article;
	}
}
