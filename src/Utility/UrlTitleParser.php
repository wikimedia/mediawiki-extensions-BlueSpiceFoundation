<?php

namespace BlueSpice\Utility;

use MWException;

class UrlTitleParser {
	protected $config;
	protected $url;

	/**
	 * TitleParse constructor.
	 * @param string $url
	 * @param \Config $config
	 */
	public function __construct( $url, $config ) {
		$this->config = $config;
		$this->url = $url;
	}

	/**
	 * Parses the title.
	 * @return \Title
	 * @throws Exception No valid title
	 */
	public function parseTitle() {
		$titleText = '';
		$parsedUrl = wfParseUrl( $this->url );

		if( isset( $parsedUrl['query'] ) ) {
			$queryString = wfCgiToArray( $parsedUrl['query'] );

			if( isset( $queryString['title'] ) ) {
				$titleText = $queryString['title'];
			} else {
				$titleText = $this->removeScriptPathFromUrl(
					$this->config->get( 'ScriptPath' ),
					$parsedUrl['path']
				);
			}
		} else {
			$titleText = $this->removeScriptPathFromUrl(
				$this->config->get( 'Server' ) . $this->config->get( 'ScriptPath' ),
				$this->url
			);
		}

		$decodedTitleText = urldecode( $titleText );

		if( empty( $decodedTitleText ) || $decodedTitleText == '/' || $decodedTitleText == $this->url ) {
			throw new MWException( "Did not find suitable title in '$decodedTitleText'" );
		}

		$title = \Title::newFromText( $decodedTitleText );
		if( $title instanceof \Title === false ) {
			throw new MWException( "Could not create title from '$decodedTitleText'" );
		}

		return $title;
	}

	private function removeScriptPathFromUrl( $scriptPath, $url ) {
		if ( substr( $url, 0, strlen( $scriptPath ) ) == $scriptPath ) {
			$url = substr( $url, strlen( $scriptPath ) );
		}

		return $url;
	}
}
