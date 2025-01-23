<?php

/**
 * Hook handler base class for MediaWiki hook ImagePageFileHistoryLine
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
use File;
use ImageHistoryList;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class ImagePageFileHistoryLine extends Hook {
	/** @var ImageHistoryList */
	protected $historyList;
	/** @var File */
	protected $file;
	/** @var string */
	protected $row;
	/** @var string */
	protected $rowClass;

	/**
	 * @param ImageHistoryList $historyList
	 * @param File $file
	 * @param string &$row
	 * @param string &$rowClass
	 * @return bool
	 */
	public static function callback( $historyList, $file, &$row, &$rowClass ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$historyList,
			$file,
			$row,
			$rowClass
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param ImageHistoryList $historyList
	 * @param File $file
	 * @param string &$row
	 * @param string &$rowClass
	 */
	public function __construct( $context, $config, $historyList, $file, &$row, &$rowClass ) {
		parent::__construct( $context, $config );

		$this->historyList = $historyList;
		$this->file = $file;
		$this->row =& $row;
		$this->rowClass =& $rowClass;
	}

}
