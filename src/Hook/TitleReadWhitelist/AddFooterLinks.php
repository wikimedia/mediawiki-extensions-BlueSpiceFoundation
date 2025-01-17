<?php

namespace BlueSpice\Hook\TitleReadWhitelist;

use BlueSpice\Hook\TitleReadWhitelist;
use MediaWiki\Title\Title;

class AddFooterLinks extends TitleReadWhitelist {

	/**
	 * @var array
	 */
	protected $footerLinks = [
		'privacy' => 'privacypage',
		'aboutsite' => 'aboutpage',
		'disclaimers' => 'disclaimerpage'
	];

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		// We require the Message subsystem to be available for this code.
		// In rare cases this code gets called pre-maturely. E.g. `onBeforeInitialize`
		// by "Extension:PluggableAuth".
		// We can safely bail out in such cases.
		//
		// `$wgFullyInitialised` has no default in `$IP/includes/DefaultSettings.php`
		// It only gets defined in `$IP/includes/Setup.php`
		if ( !$this->getConfig()->has( 'FullyInitialised' ) ) {
			return true;
		}
		foreach ( $this->getFooterTitles() as $title ) {
			if ( $title->equals( $this->title ) ) {
				return false;
			}
		}
		return true;
	}

	protected function doProcess() {
		$this->whitelisted = true;
	}

	/**
	 * @return Title[]
	 */
	protected function getFooterTitles() {
		$titles = [];
		foreach ( $this->footerLinks as $desc => $page ) {
			if ( $this->msg( $desc )->inContentLanguage()->isDisabled() ) {
				continue;
			}
			$title = Title::newFromText( $this->msg( $page )->inContentLanguage()->text() );
			if ( !$title ) {
				continue;
			}
			$titles[] = $title;
		}
		return $titles;
	}

}
