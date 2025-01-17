<?php

namespace BlueSpice\Task\WikiPage;

use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use MediaWiki\Title\Title;

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
