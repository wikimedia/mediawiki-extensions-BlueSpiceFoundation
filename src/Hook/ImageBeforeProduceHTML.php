<?php
/**
 * Hook handler base class for MediaWiki hook ImageBeforeProduceHTML
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
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use DummyLinker;
use File;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Parser\Parser;
use MediaWiki\Title\Title;

abstract class ImageBeforeProduceHTML extends Hook {

	/**
	 *
	 * @var DummyLinker
	 */
	protected $linker = null;

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var File
	 */
	protected $file = null;

	/**
	 *
	 * @var array
	 */
	protected $frameParams = [];

	/**
	 *
	 * @var array
	 */
	protected $handlerParams = [];

	/**
	 *
	 * @var string
	 */
	protected $time = '';

	/**
	 *
	 * @var strnig|null
	 */
	protected $result = '';

	/**
	 *
	 * @var Parser|null
	 */
	protected $parser = null;

	/**
	 *
	 * @var string
	 */
	protected $query = '';

	/**
	 *
	 * @var int|null
	 */
	protected $widthOption = null;

	/**
	 *
	 * @param DummyLinker &$linker
	 * @param Title &$title
	 * @param File &$file
	 * @param array &$frameParams
	 * @param array &$handlerParams
	 * @param string &$time
	 * @param string|null &$result
	 * @param Parser|null $parser
	 * @param string &$query
	 * @param int|null &$widthOption
	 * @return bool Always true to keep hook running
	 */
	public static function callback( DummyLinker &$linker, Title &$title, &$file,
		array &$frameParams, array &$handlerParams, &$time, &$result, ?Parser $parser = null,
		&$query = '', &$widthOption = null ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$linker,
			$title,
			$file,
			$frameParams,
			$handlerParams,
			$time,
			$result,
			$parser,
			$query,
			$widthOption
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param DummyLinker &$linker
	 * @param Title &$title
	 * @param File &$file
	 * @param array &$frameParams
	 * @param array &$handlerParams
	 * @param string &$time
	 * @param string|null &$result
	 * @param Parser|null $parser
	 * @param string &$query
	 * @param int|null &$widthOption
	 */
	public function __construct( $context, $config, DummyLinker &$linker, Title &$title, &$file,
		array &$frameParams, array &$handlerParams, &$time, &$result, ?Parser $parser = null,
		&$query = '', &$widthOption = null ) {
		parent::__construct( $context, $config );

		$this->linker = &$linker;
		$this->title = &$title;
		$this->file = &$file;
		$this->frameParams = &$frameParams;
		$this->handlerParams = &$handlerParams;
		$this->time = &$time;
		$this->result = &$result;
		$this->parser = $parser;
		$this->query = &$query;
		$this->widthOption = &$widthOption;
	}
}
