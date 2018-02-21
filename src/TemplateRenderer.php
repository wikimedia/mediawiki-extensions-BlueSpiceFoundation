<?php
/**
 * TemplateRenderer base class for BlueSpice
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
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice;

abstract class TemplateRenderer extends Renderer implements ITemplateRenderer {

	protected static $cache = [];

	/**
	 * Returns a rendered template as HTML markup
	 * @return string - HTML
	 */
	public function render() {
		$content = '';
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();

		return $content;
	}

	protected function makeTagContent() {
		$content = $this->getFromCache();
		if( $content ) {
			return $content;
		}

		$content = \BSTemplateHelper::process(
			$this->getTemplateName(),
			$this->getRenderedArgs()
		);
		$this->appendCache( $content );
		return $content;
	}

	/**
	 * Returns an array of arguments
	 * @return array
	 */
	public function getArgs() {
		return $this->args;
	}

	/**
	 * Returns an array of arguments ready to render into a template
	 * @return array
	 */
	protected function getRenderedArgs() {
		$args = [];
		foreach( $this->getArgs() as $name => $val ) {
			$method = "render_$name";
			$renderedVal = $val;
			if( is_callable( array( $this, $method ) ) ) {
				$renderedVal = $this->$method( $val );
			}
			\Hooks::run( 'BSTemplateRendererGetRenderedArgs', [
				$this,
				$name,
				$val,
				&$renderedVal,
			]);
			$args[ $name ] = $renderedVal;
		}
		return $args;
	}

	protected function getFromCache() {
		if( !$cacheKey = $this->getCacheKey() ) {
			return false;
		}
		if( $this->hasCacheEntry() ) {
			return static::$cache[$cacheKey];
		}
		static::$cache[$cacheKey] = \BsCacheHelper::get( $cacheKey );
		return static::$cache[$cacheKey];
	}

	protected function hasCacheEntry() {
		if( !$cacheKey = $this->getCacheKey() ) {
			return false;
		}
		return isset( static::$cache[$cacheKey] );
	}

	protected function getCacheKey() {
		return false;
	}

	protected function getCacheExpiryTime() {
		return 60*1440; //24h - max
	}

	/**
	 *
	 * @param string $content
	 * @return boolean
	 */
	protected function appendCache( $content ) {
		if( !$cacheKey = $this->getCacheKey() ) {
			return false;
		}
		\BsCacheHelper::set(
			$cacheKey,
			$content,
			$this->getCacheExpiryTime()
		);
		static::$cache[$cacheKey] = $content;
		return true;
	}

	/**
	 * Invalidates the cache entry for this renderer
	 * @return boolean
	 */
	public function invalidate() {
		if( !$cacheKey = $this->getCacheKey() ) {
			return false;
		}

		if( isset( static::$cache[$cacheKey] ) ) {
			unset( static::$cache[$cacheKey] );
		}
		return \BsCacheHelper::invalidateCache( $cacheKey );
	}
}
