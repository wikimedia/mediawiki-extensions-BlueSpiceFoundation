<?php

namespace BlueSpice\Tag\MarkerType;

use BlueSpice\Tag\MarkerType;

/**
 * @deprecated Use mediawiki-component-generictaghandler instead
 */
class None extends MarkerType {
	public function __toString() {
		return 'none';
	}
}
