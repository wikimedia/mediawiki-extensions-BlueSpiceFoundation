<?php

//derived from https://github.com/vedmaka/mediawiki-extension-Mustache_i18n

namespace BlueSpice;

class TemplateParser extends \TemplateParser {

	protected function compile( $code ) {
		if ( !class_exists( 'LightnCandy' ) ) {
			throw new \RuntimeException( 'LightnCandy class not defined' );
		}

		return \LightnCandy::compile(
			$code, array(
			  // Do not add more flags here without discussion.
			  // If you do add more flags, be sure to update unit tests as well.
			  'flags' => \LightnCandy::FLAG_ERROR_EXCEPTION,
			  'helpers' => array(
				  '_' => function( $msg ) {
					  if ( count( $msg ) > 1 ) {
						  $msgKey = array_shift( $msg );
						  return wfMessage( $msgKey, $msg )->plain();
					  } else {
						  return wfMessage( $msg )->plain();
					  }
				  },
				  '__' => function( $msg ) {
					  return wfMessage( $msg )->parse();
				  },
			  )
			)
		);
	}

}
