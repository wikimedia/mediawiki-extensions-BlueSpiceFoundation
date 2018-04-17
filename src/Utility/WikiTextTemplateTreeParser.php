<?php

namespace BlueSpice\Utility;

class WikiTextTemplateTreeParser {

	/**
	 *
	 * @var string
	 */
	protected $wikiText = '';

	/**
	 *
	 * @var string
	 */
	protected $currentWikiText = '';

	/**
	 *
	 * @var array
	 */
	protected $result = [];

	/**
	 *
	 * @var array
	 */
	protected $templateMap = [];

	/**
	 *
	 * @param string $wikiText
	 */
	public function __construct( $wikiText ) {
		$this->wikiText = $wikiText;
	}

	/**
	 * @return array
	 */
	public function getArray() {
		$this->currentWikiText = $this->wikiText;
		while( $this->currentWikiTextContainsLeafTemplates() ) {
			$this->findAndReplaceLeafTemplates();
		}

		$marker = $this->findMarkers( $this->currentWikiText );
		$this->resolveAndAddToResult( $marker, $this->result );

		return $this->result;
	}

	protected function currentWikiTextContainsLeafTemplates() {
		return
			preg_match( "/{{([^{]*?)}}/", $this->currentWikiText )
			!== 0;
	}

	protected $counter = 0;

	protected function findAndReplaceLeafTemplates() {
		$this->currentWikiText = preg_replace_callback(
			"/{{([^{]*?)}}/",
			function( $matches ) {
				$marker = "###T:{$this->counter}:T###";
				$this->counter++;
				$this->templateMap[$marker] = $matches[0];

				return $marker;
			},
			$this->currentWikiText
		);
	}

	protected function findMarkers( $wikiText ) {
		$marker = [];
		preg_replace_callback(
			"/###T:(.*?):T###/",
			function( $matches ) use ( &$marker ) {
				$marker[] = $matches[0];
				return $matches[0];
			},
			$wikiText
		);
		return $marker;
	}

	protected function resolveAndAddToResult( $markers, &$result ) {
		foreach( $markers as $marker ) {
			$descriptor = [
				'name' => '',
				'params' => []
			];

			$wikiTextTemplateCall = $this->templateMap[$marker];
			$innerWikiText = trim( $wikiTextTemplateCall, '{}' );
			$parts = explode( '|', $innerWikiText );

			$templateName = array_shift( $parts );
			$descriptor['name'] = trim( $templateName );
			foreach( $parts as $kvPair ) {
				$kv = explode( '=', $kvPair, 2 );
				$paramName = trim( $kv[0] );
				$paramValue = trim( $kv[1] );

				$nestedMarkers = $this->findMarkers( $paramValue );
				if( !empty( $nestedMarkers ) ) {
					$nestedResult = [];
					$this->resolveAndAddToResult(
						$nestedMarkers,
						$nestedResult
					);
					$paramValue = $nestedResult;
				}

				$descriptor['params'][$paramName] = $paramValue;
			}

			$result[] = $descriptor;
		}
	}
}
