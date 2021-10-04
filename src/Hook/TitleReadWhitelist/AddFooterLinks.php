<?php

namespace BlueSpice\Hook\TitleReadWhitelist;

use BlueSpice\Hook\TitleReadWhitelist;
use Title;

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
