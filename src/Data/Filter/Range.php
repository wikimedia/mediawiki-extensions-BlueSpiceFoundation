<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

abstract class Range extends Filter {
	const COMPARISON_LOWER_THAN = 'lt';
	const COMPARISON_GREATER_THAN = 'gt';
}
