<?php
/**
 * Hook handler base class for MediaWiki hook ParserGetVariableValueSwitch
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
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

abstract class ParserGetVariableValueSwitch extends Hook {

	/**
	 *
	 * @var Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var array
	 */
	protected $variableCache = [];

	/**
	 *
	 * @var string
	 */
	protected $magicWordId = '';

	/**
	 *
	 * @var string
	 */
	protected $ret = '';

	/**
	 *
	 * @var PPFrame
	 */
	protected $frame = null;

	/**
	 *
	 * @param Parser $parser
	 * @param array &$variableCache
	 * @param string $magicWordId
	 * @param string &$ret
	 * @param PPFrame $frame
	 * @return bool
	 */
	public static function callback( $parser, &$variableCache, $magicWordId, &$ret, $frame ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$parser,
			$variableCache,
			$magicWordId,
			$ret,
			$frame
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Parser $parser
	 * @param array &$variableCache
	 * @param string $magicWordId
	 * @param string &$ret
	 * @param PPFrame $frame
	 */
	public function __construct( $context, $config, $parser, &$variableCache, $magicWordId,
		&$ret, $frame ) {
		parent::__construct( $context, $config );

		$this->parser = $parser;
		$this->variableCache =& $variableCache;
		$this->magicWordId = $magicWordId;
		$this->ret =& $ret;
		$this->frame = $frame;
	}
}
