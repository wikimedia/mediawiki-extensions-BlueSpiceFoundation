<?php
/**
 * Hook handler base class for MediaWiki hook MakeGlobalVariablesScript
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
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Output\OutputPage;

abstract class MakeGlobalVariablesScript extends Hook {

	/**
	 *
	 * @var array
	 */
	protected $vars = [];

	/**
	 *
	 * @var OutputPage
	 */
	protected $out = null;

	/**
	 *
	 * @param array &$vars
	 * @param OutputPage $out
	 * @return bool
	 */
	public static function callback( &$vars, $out ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$vars,
			$out
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param array &$vars
	 * @param OutputPage $out
	 */
	public function __construct( $context, $config, &$vars, $out ) {
		parent::__construct( $context, $config );

		$this->vars =& $vars;
		$this->out = $out;
	}
}
