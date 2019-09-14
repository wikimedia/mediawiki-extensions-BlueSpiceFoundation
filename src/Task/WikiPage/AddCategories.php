<?php

namespace BlueSpice\Task\WikiPage;

use Title;
use Status;
use WikiPage;
use Category;
use WikiCategoryPage;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;

class AddCategories extends SetCategories {

	/**
	 * @var WikiCategoryPage
	 */
	private $wikiCategoryPage = null;

	const PARAM_PAGE_TITLE = 'page_title';

	/**
	 * @return Status
	 */
	protected function doExecute() {
		return $this->saveWikiPage( '' );
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTaskPermissions() {
		return [ 'create' ];
	}

	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToRemove( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return [];
	}
	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToAdd( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return $categoryTitles;
	}

	/**
	 * @return array|ParamDefinition[]
	 */
	public function getArgsDefinitions() {
		$args = parent::getArgsDefinitions();
		$pageTitle = new ParamDefinition(
			ParamType::STRING,
			static::PARAM_PAGE_TITLE,
			[],
			null,
			false
		);
		$args[] = $pageTitle;

		return $args;
	}

	/**
	 * @return WikiCategoryPage
	 */
	protected function getWikiPage() {
		if ( !$this->wikiCategoryPage ) {
			$pageTitle = $this->getParam( 'page_title' );
			$category = Category::newFromName( $pageTitle );
			$this->wikiCategoryPage = WikiPage::factory( $category->getTitle() );
		}

		return $this->wikiCategoryPage;
	}

}
