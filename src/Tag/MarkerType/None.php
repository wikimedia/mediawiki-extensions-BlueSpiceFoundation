<?php

namespace BlueSpice\Tag\MarkerType;

use BlueSpice\Tag\MarkerType;

class None extends MarkerType {
	public function __toString() {
		return 'none';
	}
}
