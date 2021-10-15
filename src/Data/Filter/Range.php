<?php

namespace BlueSpice\Data\Filter;

use BlueSpice\Data\Filter;

abstract class Range extends Filter {
	public const COMPARISON_LOWER_THAN = 'lt';
	public const COMPARISON_GREATER_THAN = 'gt';
}
