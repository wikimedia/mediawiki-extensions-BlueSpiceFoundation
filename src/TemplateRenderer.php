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
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use Config;
use IContextSource;
use BlueSpice\Renderer\Params;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Linker\LinkRenderer;

abstract class TemplateRenderer extends Renderer implements ITemplateRenderer {

	protected static $cache = [];

	/**
	 *
	 * @var CacheHelper
	 */
	protected $cacheHelper = null;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 * @param CacheHelper|null $cacheHelper
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', CacheHelper $cacheHelper = null ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$this->cacheHelper = $cacheHelper;
	}
	/**
	 *
	 * @param string $name
	 * @param Services $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @param CacheHelper|null $cacheHelper
	 * @return Renderer
	 */
	public static function factory( $name, Services $services, Config $config, Params $params,
		IContextSource $context = null, LinkRenderer $linkRenderer = null,
		CacheHelper $cacheHelper = null ) {
		if ( !$context ) {
			$context = $params->get(
				static::PARAM_CONTEXT,
				false
			);
			if ( !$context instanceof IContextSource ) {
				$context = \RequestContext::getMain();
			}
		}
		if ( !$linkRenderer ) {
			$linkRenderer = $services->getLinkRenderer();
		}
		if ( !$cacheHelper ) {
			$cacheHelper = $services->getBSUtilityFactory()->getCacheHelper();
		}

		return new static( $config, $params, $linkRenderer, $context, $name, $cacheHelper );
	}

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
		static::$cache[$cacheKey] = $this->getCacheHelper()->get( $cacheKey );
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
		$this->getCacheHelper()->set(
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
		return $this->getCacheHelper()->invalidate( $cacheKey );
	}

	/**
	 *
	 * @return CacheHelper
	 */
	protected function getCacheHelper() {
		if ( !$this->cacheHelper ) {
			$this->cacheHelper = Services::getInstance()->getBSUtilityFactory()
				->getCacheHelper();
			// Deprecated since 3.1! All sub classes should be registered with a factory
			// callback and inject CacheHelper
			wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		}
		return $this->cacheHelper;
	}
}
