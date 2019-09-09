<?php

// derived from https://github.com/vedmaka/mediawiki-extension-Mustache_i18n

namespace BlueSpice;

use LightnCandy;
use RuntimeException;
use Message;
use MessageLocalizer;
use RequestContext;

class TemplateParser extends \TemplateParser implements ITemplateParser, MessageLocalizer {

	/**
	 * Compile the Mustache code into PHP code using LightnCandy
	 * @param string $code Mustache code
	 * @return string PHP code (with '<?php')
	 * @throws RuntimeException
	 */
	protected function compile( $code ) {
		if ( !class_exists( 'LightnCandy' ) ) {
			throw new RuntimeException( 'LightnCandy class not defined' );
		}
		$helpers = $this->getCompileHelpers();
		return LightnCandy::compile( $code, [
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
	 * @param mixed $args,...
	 * @return Message
	 */
	public function msg( $key ) {
		return call_user_func_array(
			[ RequestContext::getMain(), 'msg' ],
			func_get_args()
		);
	}

}
