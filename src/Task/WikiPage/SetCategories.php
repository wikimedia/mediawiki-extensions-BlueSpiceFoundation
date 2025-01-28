<?php

namespace BlueSpice\Task\WikiPage;

use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\Task\WikiPage as WikiPageTask;
use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MWException;

class SetCategories extends WikiPageTask {
	public const PARAM_CATEGORIES = 'categories';

	/**
	 * @return Status
	 * @throws MWException
	 */
	protected function doExecute() {
		$status = Status::newGood();
		$categories = $this->getParam( static::PARAM_CATEGORIES );
		$this->logger->debug( 'getParams', [ 'categories' => $categories ] );
		$categoryTitles = $invalid = [];
		foreach ( $categories as $title ) {
			if ( !$title instanceof Title ) {
				if ( method_exists( $title, '__toString' ) || is_string( $title ) ) {
					$invalid[] = $title;
				}
				continue;
			}

			if ( $title->getNamespace() === NS_CATEGORY ) {
				$categoryTitles[] = $title;
				continue;
			}
			$category = Title::makeTitle( NS_CATEGORY, $title->getText() );
			if ( !$category ) {
				$invalid[] = $title->getFullText();
				continue;
			}
			$categoryTitles[] = $category;
		}

		if ( !empty( $invalid ) ) {
			$this->logger->debug( 'invalidCategories', [ 'categories' => $categories ] );
			$status->error( $this->msg(
				'bs-wikipage-tasks-error-categories-not-valid',
				implode( ', ', $invalid )
			) );
		}
		$this->logger->debug( 'status', [ 'status' => $status ] );
		if ( !$status->isOK() ) {
			return $status;
		}

		$wikiText = $this->fetchCurrentRevisionWikiText();
		$helper = $this->getCategoryLinksHelper( $wikiText );

		$targetsToRemove = $this->getCategoriesToRemove( $helper, $categoryTitles );
		$this->logger->debug( 'beforeRemoveTargets', [ 'targets' => $targetsToRemove ] );
		$helper->removeTargets( $targetsToRemove );

		$targetsToAdd = $this->getCategoriesToAdd( $helper, $categoryTitles );
		$this->logger->debug( 'beforeAddTargets', [ 'targets' => $targetsToAdd ] );
		$helper->addTargets( $targetsToAdd );

		return $this->saveWikiPage( $helper->getWikitext() );
	}

	/**
	 *
	 * @param Title[] $array1
	 * @param Title[] $array2
	 * @return Title[]
	 */
	protected function filterDiffTargets( array $array1, array $array2 ) {
		return array_filter( $array1, static function ( Title $e ) use( $array2 ) {
			foreach ( $array2 as $target ) {
				if ( $e->equals( $target ) ) {
					return false;
				}
			}
			return true;
		} );
	}

	/**
	 *
	 * @param string $wikiText
	 * @return CategoryLinksHelper
	 */
	protected function getCategoryLinksHelper( $wikiText ) {
		return $this->getServices()->getService( 'BSUtilityFactory' )
			->getWikiTextLinksHelper( $wikiText )->getCategoryLinksHelper();
	}

	/**
	 *
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions() {
		$categories = new \BSTitleListParam(
			ParamType::TITLE_LIST,
			static::PARAM_CATEGORIES,
			[],
			null,
			true
		);

		$validator = new \BSTitleValidator();
		$validator->setOptions( [ 'hastoexist' => false ] );
		$categories->setValueValidator( $validator );

		return [
			$categories
		];
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTaskPermissions() {
		return [ 'edit' ];
	}

	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToRemove( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return array_values( $this->filterDiffTargets(
			$helper->getTargets(),
			$categoryTitles
		) );
	}

	/**
	 *
	 * @param CategoryLinksHelper $helper
	 * @param Title[] $categoryTitles
	 * @return Title[]
	 */
	protected function getCategoriesToAdd( CategoryLinksHelper $helper, $categoryTitles = [] ) {
		return array_values( $this->filterDiffTargets(
			$categoryTitles,
			$helper->getTargets()
		) );
	}

	/**
	 *
	 * @return string
	 */
	protected function getSaveWikiPageSummary() {
		$lang = $this->getContext()->getConfig()->get( 'LanguageCode' );
		return $this->msg( 'bs-wikipage-tasks-setcategories-edit-summary' )
			->inLanguage( $lang )
			->text();
	}

}
