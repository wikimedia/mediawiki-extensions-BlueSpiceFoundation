<?php
/**
 * Provides the task factory class for BlueSpice.
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
 * @copyright  Copyright (C) 2019 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MWException;

class TaskFactory {

	/**
	 *
	 * @var IRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param IRegistry $registry
	 * @param Config $config
	 */
	public function __construct( $registry, $config ) {
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 *
	 * @param string $key
	 * @param Context $context
	 * @param IPermissionChecker|null $permissionChecker
	 * @return ITask
	 */
	public function get( $key, Context $context, ?IPermissionChecker $permissionChecker = null ) {
		$callback = $this->registry->getValue(
			$key,
			false
		);
		if ( !$callback ) {
			throw new MWException( "No registered task for given '$key'!" );
		}
		$instance = $callback( MediaWikiServices::getInstance(), $context, $permissionChecker );
		return $instance;
	}
}
