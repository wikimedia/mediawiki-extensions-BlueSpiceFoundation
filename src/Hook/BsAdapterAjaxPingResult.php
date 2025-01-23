<?php
/**
 * Hook handler base class for BlueSpice hook BsAdapterAjaxPingResult
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
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class BsAdapterAjaxPingResult extends Hook {

	/**
	 * Reference key of a single ping request
	 * @var string
	 */
	protected $reference = null;

	/**
	 * Params for a single ping request
	 * @var array
	 */
	protected $params = null;

	/**
	 * ID of the Article, in which context the ping was sent
	 * @var int
	 */
	protected $articleId = null;

	/**
	 * Title text of the Article, in which context the ping was sent
	 * @var string
	 */
	protected $titleText = null;

	/**
	 * Namespace index of the Article, in which context the ping was sent
	 * @var int
	 */
	protected $namespaceIndex = null;

	/**
	 * ID of the revision of the Article, in which context the ping was sent
	 * @var int
	 */
	protected $revisionId = null;

	/**
	 * Result data of the single ping request
	 * @var array
	 */
	protected $singleResults = null;

	/**
	 * Located in BSApiPingTasks::task_ping. After each single ping request was
	 * processed. Return false to set the whole ping request's success to false
	 * and abort any further processing.
	 * @param string $reference
	 * @param array $params
	 * @param int $articleId
	 * @param string $titleText
	 * @param int $namespaceIndex
	 * @param int $revisionId
	 * @param array &$singleResults
	 * @return bool
	 */
	public static function callback( $reference, $params, $articleId, $titleText, $namespaceIndex,
		$revisionId, &$singleResults ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$reference,
			$params,
			$articleId,
			$titleText,
			$namespaceIndex,
			$revisionId,
			$singleResults
		);
		return $hookHandler->process();
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string $reference
	 * @param array $params
	 * @param int $articleId
	 * @param string $titleText
	 * @param int $namespaceIndex
	 * @param int $revisionId
	 * @param array &$singleResults
	 */
	public function __construct( $context, $config, $reference, $params, $articleId, $titleText,
		$namespaceIndex, $revisionId, &$singleResults ) {
		parent::__construct( $context, $config );

		$this->reference = $reference;
		$this->params = $params;
		$this->articleId = $articleId;
		$this->titleText = $titleText;
		$this->namespaceIndex = $namespaceIndex;
		$this->revisionId = $revisionId;
		$this->singleResults = &$singleResults;
	}
}
