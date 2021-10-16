<?php

namespace BlueSpice;

use Config;
use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use MessageLocalizer;
use PageHeader\PageInfo;

/**
 * DEPRECATED
 * @deprecated since version 4.1 - use \PageHeader\PageInfo instead
 */
abstract class PageInfoElement extends PageInfo implements IPageInfoElement, MessageLocalizer {

	/**
	 * @deprecated since version 4.1 - extend PageHeader\PageInfo instead
	 * @param IContextSource $context
	 * @param Config $config
	 * @return IPageHeader
	 */
	public static function factory( IContextSource $context, Config $config ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$bsConfig = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		return new static(
			new Context( $context, $bsConfig ),
			$bsConfig
		);
	}

	/**
	 * DEPRECATED
	 * Get a Message object with context set
	 * Parameters are the same as wfMessage()
	 *
	 * @deprecated since version 4.1 - use $this->context->msg instead
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params
	 * @return Message
	 */
	public function msg( $key, ...$params ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return $this->context->msg( $key, ...$params );
	}
}
