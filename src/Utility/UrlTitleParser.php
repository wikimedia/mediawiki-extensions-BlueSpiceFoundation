<?php

namespace BlueSpice\Utility;

use MediaWiki\Config\Config;
use MediaWiki\Config\ConfigException;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MWException;

class UrlTitleParser {
	/** @var Config */
	protected $config;
	/** @var string */
	protected $url;
	/** @var bool */
	protected $failSilently;

	/**
	 * TitleParse constructor.
	 * @param string $url
	 * @param Config $config
	 * @param bool|null $failSilently If false, will trow error on invalid titles
	 */
	public function __construct( $url, $config, $failSilently = false ) {
		$this->config = $config;
		$this->url = $url;
		$this->failSilently = $failSilently;
	}

	/**
	 * Parses the title.
	 *
	 * @return Title
	 * @throws MWException|ConfigException No valid title
	 */
	public function parseTitle() {
		$titleText = trim( $this->getRawTitle(), '/' );
		$decodedTitleText = urldecode( $titleText );

		if ( empty( $decodedTitleText ) || $decodedTitleText == '/' || $decodedTitleText == $this->url ) {
			if ( $this->failSilently ) {
				return null;
			}
			throw new MWException( "Did not find suitable title in '$decodedTitleText'" );
		}

		$title = Title::newFromText( $decodedTitleText );
		if ( $title instanceof Title === false ) {
			if ( $this->failSilently ) {
				return null;
			}
			throw new MWException( "Could not create title from '$decodedTitleText'" );
		}

		return $title;
	}

	/**
	 * Get raw title name from the URL
	 * @return string
	 * @throws ConfigException
	 */
	private function getRawTitle() {
		$scriptPath = $this->config->get( 'ScriptPath' );
		$articlePath = $this->config->get( 'ArticlePath' );

		$urlUtils = MediaWikiServices::getInstance()->getUrlUtils();
		$parsedUrl = $urlUtils->parse( $this->url );
		if ( !isset( $parsedUrl['path'] ) ) {
			return '';
		}

		if ( isset( $parsedUrl['query'] ) ) {
			$queryString = wfCgiToArray( $parsedUrl['query'] );
			if ( isset( $queryString['title'] ) ) {
				// {domain}/{wgScriptPath}/index.php?title=Title
				return $queryString['title'];
			}
		}
		if ( strpos( $parsedUrl['path'], "$scriptPath/" ) === 0 ) {
			// {domain}/{wgScriptPath}/index.php/Title
			return $this->removeFromUrl( "$scriptPath/index.php", $parsedUrl['path'] );
		}
		// {domain}/{wgArticlePath}/Title
		// /wiki/$1 => \/wiki\/(.*)
		$articlePath = str_replace( '\$1', "(.*)", preg_quote( $articlePath, '/' ) );

		return preg_replace( '/' . $articlePath . '/', '$1', $parsedUrl['path'] );
	}

	/**
	 * @param string $search Part to remove
	 * @param string $url haystack
	 * @return string
	 */
	private function removeFromUrl( $search, $url ) {
		if ( substr( $url, 0, strlen( $search ) ) === $search ) {
			return substr( $url, strlen( $search ) );
		}

		return $url;
	}
}
