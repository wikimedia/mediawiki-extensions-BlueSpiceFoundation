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
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice;

use BlueSpice\Renderer\Params;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\MediaWikiServices;

abstract class TemplateRenderer extends Renderer implements ITemplateRenderer {

	protected static $cache = [];

	/**
	 *
	 * @var CacheHelper
	 */
	protected $cacheHelper = null;

	/**
	 *
	 * @var TemplateFactory
	 */
	protected $templateFactory = null;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name
	 * @param CacheHelper|null $cacheHelper
	 * @param TemplateFactory|null $templateFactory
	 */
	protected function __construct( Config $config, Params $params,
		?LinkRenderer $linkRenderer = null, ?IContextSource $context = null,
		$name = '', ?CacheHelper $cacheHelper = null,
		?TemplateFactory $templateFactory = null ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$this->cacheHelper = $cacheHelper;
		$this->templateFactory = $templateFactory;
	}

	/**
	 *
	 * @param string $name
	 * @param MediaWikiServices $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @param CacheHelper|null $cacheHelper
	 * @param TemplateFactory|null $templateFactory
	 * @return Renderer
	 */
	public static function factory( $name, MediaWikiServices $services, Config $config,
		Params $params, ?IContextSource $context = null, ?LinkRenderer $linkRenderer = null,
		?CacheHelper $cacheHelper = null, ?TemplateFactory $templateFactory = null ) {
		if ( !$context ) {
			$context = $params->get(
				static::PARAM_CONTEXT,
				false
			);
			if ( !$context instanceof IContextSource ) {
				$context = RequestContext::getMain();
			}
		}
		if ( !$linkRenderer ) {
			$linkRenderer = $services->getLinkRenderer();
		}
		if ( !$cacheHelper ) {
			$cacheHelper = $services->getService( 'BSUtilityFactory' )->getCacheHelper();
		}
		if ( !$templateFactory ) {
			$templateFactory = $services->getService( 'BSTemplateFactory' );
		}

		return new static(
			$config,
			$params,
			$linkRenderer,
			$context,
			$name,
			$cacheHelper,
			$templateFactory
		);
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
		if ( $content ) {
			return $content;
		}

		$template = $this->getTemplateFactory()->get( $this->getTemplateName() );
		$content = $template->process(
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
		foreach ( $this->getArgs() as $name => $val ) {
			$method = "render_$name";
			$renderedVal = $val;
			if ( is_callable( [ $this, $method ] ) ) {
				$renderedVal = $this->$method( $val );
			}
			$this->services->getHookContainer()->run(
				'BSTemplateRendererGetRenderedArgs',
				[
					$this,
					$name,
					$val,
					&$renderedVal,
				]
			);
			$args[ $name ] = $renderedVal;
		}
		return $args;
	}

	/**
	 *
	 * @return string|false
	 */
	protected function getFromCache() {
		$cacheKey = $this->getCacheKey();
		if ( !$cacheKey ) {
			return false;
		}
		if ( $this->hasCacheEntry() ) {
			return static::$cache[$cacheKey];
		}
		static::$cache[$cacheKey] = $this->getCacheHelper()->get( $cacheKey );
		return static::$cache[$cacheKey];
	}

	protected function hasCacheEntry() {
		$cacheKey = $this->getCacheKey();
		if ( !$cacheKey ) {
			return false;
		}
		return isset( static::$cache[$cacheKey] );
	}

	/**
	 *
	 * @return bool
	 */
	protected function getCacheKey() {
		return false;
	}

	/**
	 *
	 * @return int
	 */
	protected function getCacheExpiryTime() {
		// 24h - max
		return 60 * 1440;
	}

	/**
	 *
	 * @param string $content
	 * @return bool
	 */
	protected function appendCache( $content ) {
		$cacheKey = $this->getCacheKey();
		if ( !$cacheKey ) {
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
	 * @return bool
	 */
	public function invalidate() {
		$cacheKey = $this->getCacheKey();
		if ( !$cacheKey ) {
			return false;
		}

		if ( isset( static::$cache[$cacheKey] ) ) {
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
			$this->cacheHelper = $this->services->getService( 'BSUtilityFactory' )
				->getCacheHelper();
			// Deprecated since 3.1! All sub classes should be registered with a factory
			// callback and inject CacheHelper
			wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		}
		return $this->cacheHelper;
	}

	/**
	 *
	 * @return TemplateFactory
	 */
	protected function getTemplateFactory() {
		if ( !$this->templateFactory ) {
			$this->templateFactory = $this->services->getService( 'BSTemplateFactory' );
			// Deprecated since 3.1! All sub classes should be registered with a factory
			// callback and inject TemplateFactory
			wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		}
		return $this->templateFactory;
	}
}
