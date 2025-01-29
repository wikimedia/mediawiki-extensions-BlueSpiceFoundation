<?php
/**
 * Hook handler base class for MediaWiki hook SecondaryDataUpdates
 * (https://www.mediawiki.org/wiki/Manual:Hooks/SecondaryDataUpdates)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License
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
 * @author     Oleksandr Pinchuk <intracomof@gmail.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2019 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Content\Content;
use MediaWiki\Context\IContextSource;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Title\Title;

abstract class SecondaryDataUpdates extends Hook {

	/** @var Title */
	protected $title;

	/** @var Content|null */
	protected $oldContent;

	/** @var bool */
	protected $recursive;

	/** @var ParserOutput */
	protected $parserOutput;

	/** @var array */
	protected $updates;

	/**
	 * @param Title $title
	 * @param Content $oldContent
	 * @param bool $recursive
	 * @param ParserOutput $parserOutput
	 * @param array &$updates
	 */
	public static function callback( Title $title, $oldContent,
		bool $recursive, ParserOutput $parserOutput, array &$updates
	) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$oldContent,
			$recursive,
			$parserOutput,
			$updates
		);
		$hookHandler->process();
	}

	/**
	 * SecondaryDataUpdates constructor.
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title $title
	 * @param Content $oldContent
	 * @param bool $recursive
	 * @param ParserOutput $parserOutput
	 * @param array &$updates
	 */
	public function __construct( $context, $config, Title $title,
		$oldContent, bool $recursive, ParserOutput $parserOutput, array &$updates
	) {
		parent::__construct( $context, $config );
		$this->title = $title;
		$this->oldContent = $oldContent;
		$this->recursive = $recursive;
		$this->parserOutput = $parserOutput;
		$this->updates = &$updates;
	}

}
