<?php

namespace BlueSpice\Tag\MarkerType;

use BlueSpice\Tag\MarkerType;

/**
 * @deprecated Use mediawiki-component-generictaghandler instead
 */
class General extends MarkerType {
	public function __toString() {
		return 'general';
	}
}
