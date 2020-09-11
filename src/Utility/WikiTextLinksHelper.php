<?php

namespace BlueSpice\Utility;

use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\FileLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\InterlanguageLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\InterwikiLinksHelper;
use MediaWiki\MediaWikiServices;

class WikiTextLinksHelper {

	/**
	 *
	 * @var string
	 */
	protected $wikitext = '';

	protected $categories = null;
	protected $links = null;
	protected $files = null;
	protected $interwikiLinks = null;
	protected $interlanguageLinks = null;

	/**
	 *
	 * @param string &$wikitext
	 */
	public function __construct( &$wikitext ) {
		$this->wikitext =& $wikitext;
	}

	/**
	 *
	 * @return CategoryLinksHelper
	 */
	public function getCategoryLinksHelper() {
		if ( $this->categories ) {
			return $this->categories;
		}
		$this->categories = new CategoryLinksHelper( $this->wikitext );
		return $this->categories;
	}

	/**
	 *
	 * @return InternalLinksHelper
	 */
	public function getInternalLinksHelper() {
		if ( $this->links ) {
			return $this->links;
		}
		$this->links = new InternalLinksHelper( $this->wikitext );
		return $this->links;
	}

	/**
	 *
	 * @return FileLinksHelper
	 */
	public function getFileLinksHelper() {
		if ( $this->files ) {
			return $this->files;
		}
		$this->files = new FileLinksHelper( $this->wikitext );
		return $this->files;
	}

	/**
	 *
	 * @return InterwikiLinksHelper
	 */
	public function getInterwikiLinksHelper() {
		if ( $this->interwikiLinks ) {
			return $this->interwikiLinks;
		}
		$this->interwikiLinks = new InterwikiLinksHelper(
			$this->wikitext,
			MediaWikiServices::getInstance()
		);
		return $this->interwikiLinks;
	}

	/**
	 *
	 * @return InterlanguageLinksHelper
	 */
	public function getLanguageLinksHelper() {
		if ( $this->interlanguageLinks ) {
			return $this->interlanguageLinks;
		}
		$this->interlanguageLinks = new InterlanguageLinksHelper(
			$this->wikitext,
			MediaWikiServices::getInstance()
		);
		return $this->interlanguageLinks;
	}
}
