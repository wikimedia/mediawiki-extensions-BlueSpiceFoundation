<?php

namespace BlueSpice\Task\WikiPage;

use Title;
use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;

class RemoveCategories extends SetCategories {

	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToRemove( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return $categoryTitles;
	}

	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToAdd( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return [];
	}
}
