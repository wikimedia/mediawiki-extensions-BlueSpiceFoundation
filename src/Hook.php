<?php
/**
 * Hook handler base class for BlueSpice
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
namespace BlueSpice;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MessageLocalizer;

abstract class Hook implements MessageLocalizer {

	/**
	 *
	 * @var IContextSource
	 */
	private $context = null;

	/**
	 *
	 * @var Config
	 */
	private $config = null;

	/**
	 * Normally both parameters are NULL on instantiation. This is because we
	 * perform a lazy loading out of performance reasons. But for the sake of
	 * testablity we keep the DI here
	 * @param IContextSource $context
	 * @param Config $config
	 */
	public function __construct( $context, $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @return IContextSource
	 */
	protected function getContext() {
		if ( $this->context instanceof IContextSource === false ) {
			$this->context = RequestContext::getMain();
		}
		return $this->context;
	}

	/**
	 *
	 * @var string
	 */
	protected static $configName = 'bsg';

	/**
	 *
	 * @return Config
	 */
	protected function getConfig() {
		if ( $this->config instanceof Config === false ) {
			$this->config = $this->getServices()->getConfigFactory()->makeConfig(
				static::$configName
			);
		}

		return $this->config;
	}

	/**
	 *
	 * @return MediaWikiServices
	 */
	public function getServices() {
		return MediaWikiServices::getInstance();
	}

	/**
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params Normal message parameters
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		return $this->getContext()->msg( $key, ...$params );
	}

	public function process() {
		if ( $this->skipProcessingForAnon() ) {
			if ( !$this->getContext()->getUser()->isRegistered() ) {
				return true;
			}
		}

		if ( $this->skipProcessingForSpecialPages() ) {
			if ( $this->getContext()->getTitle()->isSpecialPage() ) {
				return true;
			}
		}

		if ( $this->skipProcessing() ) {
			return true;
		}

		\Profiler::instance()->scopedProfileIn( "Hook " . __METHOD__ );
		$result = $this->doProcess();
		return $result;
	}

	abstract protected function doProcess();

	/**
	 * Allow subclasses to define a skip condition
	 * @return bool
	 */
	protected function skipProcessing() {
		return false;
	}

	/**
	 * Convenience method for subclasses
	 * @return bool
	 */
	protected function skipProcessingForAnon() {
		return false;
	}

	/**
	 * Convenience method for subclasses
	 * @return bool
	 */
	protected function skipProcessingForSpecialPages() {
		return false;
	}
}
