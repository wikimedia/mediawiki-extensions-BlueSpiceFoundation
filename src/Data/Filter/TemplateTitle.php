<?php

namespace BlueSpice\Data\Filter;

class TemplateTitle extends Title {
	/**
	 *
	 * @return int
	 */
	protected function getDefaultTitleNamespace() {
		return NS_TEMPLATE;
	}
}
