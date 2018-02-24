<?php

namespace BlueSpice\Tag\MarkerType;

use BlueSpice\Tag\MarkerType;

class NoWiki extends MarkerType {
	public function __toString() {
		return 'nowiki';
	}
}
