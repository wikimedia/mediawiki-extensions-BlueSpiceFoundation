<?php

// derived from https://github.com/vedmaka/mediawiki-extension-Mustache_i18n

namespace BlueSpice;

use FileContentsHasher;
use Message;
use MessageLocalizer;
use RequestContext;
use ResourceLoaderContext;
use RuntimeException;

class TemplateParser extends \TemplateParser implements ITemplateParser, MessageLocalizer {

	/**
	 * @param string|null $templateDir
	 * @param bool $forceRecompile
	 */
	public function __construct( $templateDir = null, $forceRecompile = false ) {
		// remove trailing slashes
		if ( !empty( $templateDir ) ) {
			$templateDir = rtrim( $templateDir, '/' );
		}
		// always recompile, because this compiler simply does not work - at least
		// with template system
		$forceRecompile = true;
		parent::__construct( $templateDir, $forceRecompile );
	}

	/**
	 * Compile the Mustache code into PHP code using LightnCandy
	 * @param string $templateName Mustache code
	 * @return string PHP code (with '<?php')
	 * @throws RuntimeException
	 */
	protected function compile( $templateName ) {
		$filename = $this->getTemplateFilename( $templateName );
		$helpers = $this->getCompileHelpers();
		if ( class_exists( '\LightnCandy\LightnCandy' ) ) {
			// MediaWiki 1.35+
			$class = '\LightnCandy\LightnCandy';
		} else {
			$class = '\LightnCandy';
		}
		$files = [ $filename ];
		$contents = file_get_contents( $filename );
		$compiled = $class::compile( $contents, [
			'flags' => $this->compileFlags,
			'basedir' => $this->templateDir,
			'fileext' => '.mustache',
			'helpers' => $helpers,
			'partialresolver' => function ( $cx, $partialName ) use ( $templateName, &$files ) {
				$filename = "{$this->templateDir}/{$partialName}.mustache";
				if ( !file_exists( $filename ) ) {
					throw new RuntimeException( sprintf(
						'Could not compile template `%s`: Could not find partial `%s` at %s',
						$templateName,
						$partialName,
						$filename
					) );
				}

				$fileContents = file_get_contents( $filename );

				if ( $fileContents === false ) {
					throw new RuntimeException( sprintf(
						'Could not compile template `%s`: Could not find partial `%s` at %s',
						$templateName,
						$partialName,
						$filename
					) );
				}

				$files[] = $filename;

				return $fileContents;
			}
		] );
		if ( !$compiled ) {
			// This shouldn't happen because LightnCandy::FLAG_ERROR_EXCEPTION is set
			// Errors should throw exceptions instead of returning false
			// Check anyway for paranoia
			throw new RuntimeException( "Could not compile template `{$filename}`" );
		}

		return [
			'phpCode' => $compiled,
			'files' => $files,
			'filesHash' => FileContentsHasher::getFileContentsHash( $files ),
		];
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
