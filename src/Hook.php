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
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;

use MediaWiki\MediaWikiServices;

abstract class Hook {

	/**
	 *
	 * @var \IContextSource
	 */
	private $context = null;

	/**
	 *
	 * @var \Config
	 */
	private $config = null;


	/**
	 * Normally both parameters are NULL on instantiation. This is because we
	 * perform a lazy loading out of performance reasons. But for the sake of
	 * testablity we keep the DI here
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( $context, $config ) {
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 *
	 * @return \IContextSource
	 */
	protected function getContext() {
		if( $this->context instanceof \IContextSource === false ) {
			$this->context = \RequestContext::getMain();
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
	 * @return \Config
	 */
	protected function getConfig() {
		if( $this->config instanceof \Config === false ) {
			$this->config = \MediaWiki\MediaWikiServices::getInstance()
				->getConfigFactory()->makeConfig( static::$configName );
		}

		return $this->config;
	}

	/**
	 *
	 * @return Services
	 */
	protected function getServices() {
		return Services::getInstance();
	}

	public function process() {
		if( $this->skipProcessingForAnon() ) {
			if( $this->getContext()->getUser()->isAnon() ) {
				return true;
			}
		}

		if( $this->skipProcessingForSpecialPages() ) {
			if( $this->getContext()->getTitle()->isSpecialPage() ) {
				return true;
			}
		}

		if( $this->skipProcessing() ) {
			return true;
		}

		\Profiler::instance()->scopedProfileIn( "Hook ". __METHOD__ );
		$result = $this->doProcess();
		return $result;
	}

	protected abstract function doProcess();

	/**
	 * Allow subclasses to define a skip condition
	 * @return boolean
	 */
	protected function skipProcessing() {
		return false;
	}

	/**
	 * Convenience method for subclasses
	 * @return boolean
	 */
	protected function skipProcessingForAnon() {
		return false;
	}

	/**
	 * Convenience method for subclasses
	 * @return boolean
	 */
	protected function skipProcessingForSpecialPages() {
		return false;
	}
}
