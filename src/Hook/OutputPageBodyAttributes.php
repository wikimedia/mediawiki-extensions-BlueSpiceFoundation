<?php
/**
 * Hook handler base class for MediaWiki hook OutputPageBodyAttributes
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
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2019 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Output\OutputPage;
use Skin;

abstract class OutputPageBodyAttributes extends Hook {

	/**
	 *
	 * @var OutputPage
	 */
	protected $out = null;

	/**
	 *
	 * @var Skin
	 */
	protected $skin = null;

	/**
	 *
	 * @var array
	 */
	protected $bodyAttrs = [];

	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param string &$bodyAttrs
	 * @return bool
	 */
	public static function callback( $out, $skin, &$bodyAttrs ) {
		$className = static::class;
		$hookHandler = new $className(
			$out->getContext(),
			null,
			$out,
			$skin,
			$bodyAttrs
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param string &$bodyAttrs
	 */
	public function __construct( $context, $config, $out, $skin, &$bodyAttrs ) {
		parent::__construct( $context, $config );
		$this->out = $out;
		$this->skin = $skin;
		$this->bodyAttrs =& $bodyAttrs;
	}
}
