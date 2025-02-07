<?php
/**
 * Hook handler base class for MediaWiki hook BeforeParserFetchTemplateAndTitle
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2019 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Parser\Parser;
use MediaWiki\Title\Title;

abstract class BeforeParserFetchTemplateAndTitle extends Hook {

	/**
	 *
	 * @var Parser
	 */
	protected $parser = null;

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 * @var bool
	 */
	protected $skip = false;

	/**
	 * @var bool|int
	 */
	protected $id = false;

	/**
	 * @param Parser $parser
	 * @param Title $title
	 * @param bool &$skip
	 * @param bool|int &$id
	 * @return bool
	 */
	public static function callback( Parser $parser, $title, &$skip, &$id ) {
		$hookHandler = new static(
			null,
			null,
			$parser,
			$title,
			$skip,
			$id
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Parser $parser
	 * @param Title $title
	 * @param bool &$skip
	 * @param bool|int &$id
	 */
	public function __construct( $context, $config, Parser $parser, $title, &$skip, &$id ) {
		parent::__construct( $context, $config );

		$this->parser = $parser;
		$this->title = $title;
		$this->skip =& $skip;
		$this->id =& $id;
	}
}
