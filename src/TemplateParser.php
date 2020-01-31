<?php

// derived from https://github.com/vedmaka/mediawiki-extension-Mustache_i18n

namespace BlueSpice;

use Message;
use MessageLocalizer;
use RequestContext;
use RuntimeException;

class TemplateParser extends \TemplateParser implements ITemplateParser, MessageLocalizer {

	/**
	 * Compile the Mustache code into PHP code using LightnCandy
	 * @param string $code Mustache code
	 * @param string $filename File name the code came from; only used for error reporting
	 * @return string PHP code (with '<?php')
	 * @throws RuntimeException
	 */
	protected function compile( $code, $filename ) {
		$helpers = $this->getCompileHelpers();
		if ( class_exists( '\LightnCandy\LightnCandy' ) ) {
			// MediaWiki 1.35+
			$class = '\LightnCandy\LightnCandy';
		} else {
			$class = '\LightnCandy';
		}
		return $class::compile( $code, [
			'flags' => $this->compileFlags,
			'basedir' => $this->templateDir,
			'fileext' => '.mustache',
			'helpers' => $helpers,
		] );
	}

	/**
	 *
	 * @return array
	 */
	protected function getCompileHelpers() {
		$helpers = [];
		if ( RequestContext::getMain() instanceof ResourceLoaderContext ) {
			return $helpers;
		}
		$helpers['_'] = function ( $msg ) {
			$msgKey = array_shift( $msg );
			return $this->msg( $msgKey, ...$msg )->plain();
		};
		$helpers['__'] = function ( $msg ) {
			$msgKey = array_shift( $msg );
			return $this->msg( $msgKey, ...$msg )->parse();
		};
		return $helpers;
	}

	/**
	 * Get a Message object with context set
	 * Parameters are the same as wfMessage()
	 *
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		return RequestContext::getMain()->msg( $key, ...$params );
	}

}
