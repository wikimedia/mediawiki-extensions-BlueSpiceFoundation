<?php

namespace BlueSpice\Tag\MarkerType;

use BlueSpice\Tag\MarkerType;

/**
 * @deprecated Use mediawiki-component-generictaghandler instead
 */
class NoWiki extends MarkerType {
	public function __toString() {
		return 'nowiki';
	}
}
