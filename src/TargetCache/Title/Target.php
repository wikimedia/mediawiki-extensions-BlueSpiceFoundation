<?php

namespace BlueSpice\TargetCache\Title;

use MediaWiki\Title\Title;

class Target implements \BlueSpice\TargetCache\ITarget {

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @param Title $title
	 */
	public function __construct( Title $title ) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->title->getFullText();
	}

}
