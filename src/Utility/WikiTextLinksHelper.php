<?php


namespace BlueSpice\Utility;

use BlueSpice\Utility\WikiTextLinksHelper\InternalLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use BlueSpice\Utility\WikiTextLinksHelper\FileLinksHelper;

class WikiTextLinksHelper {

	/**
	 *
	 * @var string
	 */
	protected $wikitext = '';

	protected $categories = null;
	protected $links = null;
	protected $files = null;

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
		if( $this->categories ) {
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
		if( $this->links ) {
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
		if( $this->files ) {
			return $this->files;
		}
		$this->files = new FileLinksHelper( $this->wikitext );
		return $this->files;
	}

}
