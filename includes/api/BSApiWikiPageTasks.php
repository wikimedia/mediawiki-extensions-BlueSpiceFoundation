<?php
/**
 * Provides common tasks that can pe performed on a WikiPage.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

/**
 * Provides common tasks that can be performed on a WikiPage
 * @package BlueSpice_Foundation
 */
class BSApiWikiPageTasks extends BSApiTasksBase {
	protected $aTasks = array(
		'setCategories' => [
			//'permissions' => [], //TODO migrate "getRequiredTaskPermissions"
			'examples' => [
				[
					'page_id' => 3234,
					'categories' => [ 'Category A', 'Category_B' ]
				],
				[
					'page_title' => 'SomeNamespace:Some page title',
					'categories' => []
				]
			],
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				],
				'categories' => [
					'desc' => 'bs-api-task-wikipagetasks-taskData-categories',
					'type' => 'array',
					'required' => false,
					'default' => [],
				]
			]
		],
		'getExplicitCategories' => [
			'examples' => [
				[
					'page_id' => 3234
				],
				[
					'page_title' => 'SomeNamespace:Some page title',
				]
			],
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				]
			]
		],
		'addCategories' => [
			'examples' => [
				[
					'page_id' => 3234,
					'categories' => [ 'Category A', 'Category_B' ]
				],
				[
					'page_title' => 'SomeNamespace:Some page title',
					'categories' => [ 'Category A', 'Category_B' ]
				]
			],
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				],
				'categories' => [
					'desc' => 'bs-api-task-wikipagetasks-taskData-categories',
					'type' => 'array',
					'required' => false,
					'default' => [],
				]
			]
		],
		'removeCategories' => [
			'examples' => [
				[
					'page_id' => 3234,
					'categories' => [ 'Category A', 'Category_B' ]
				],
				[
					'page_title' => 'SomeNamespace:Some page title',
					'categories' => [ 'Category A', 'Category_B' ]
				]
			],
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				],
				'categories' => [
					'desc' => 'bs-api-task-wikipagetasks-taskData-categories',
					'type' => 'array',
					'required' => false,
					'default' => [],
				]
			]
		],
		'getDiscussionCount' => [
			'examples' => [
				[
					'page_id' => 3234
				],
				[
					'page_title' => 'SomeNamespace:Some page title'
				]
			],
			//'readonly' => true, //TODO migrate "$this->aReadTasks"
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				]
			]
		],
		'getTemplateTree' => [
			'examples' => [
				[
					'page_id' => 3234
				],
				[
					'page_title' => 'SomeNamespace:Some page title'
				]
			],
			'params' => [
				'page_id' => [
					'type' => 'integer',
					'required' => true,
					'alternative_to' => [ 'page_title' ]
				],
				'page_title' => [
					'type' => 'string',
					'required' => true,
					'alternative_to' => [ 'page_id' ]
				]
			]
		]
	);

	protected $aReadTasks = [
		'getDiscussionCount',
		'getExplicitCategories',
		'getTemplateTree'
	];

	/**
	 * Configures the global permission requirements
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return array(
			'setCategories' => array( 'edit' ),
			'getExplicitCategories' => array( 'read' ),
			'addCategories' => array( 'edit' ),
			'removeCategories' => array( 'edit' ),
			'getDiscussionCount' => array( 'read' ),
			'getTemplateTree' => array( 'read' )
		);
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_setCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();

		$aCategories = $oTaskData->categories;
		$aCategories = ( !is_array( $aCategories ) ) ? array() : $aCategories;

		$oTitle = $this->getTitleFromTaskData( $oTaskData );

		//Check for actual title permissions
		if ( !$oTitle->userCan( 'edit' ) ) {
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-page-edit-not-allowed',
				$oTitle->getPrefixedText()
			)->plain();
			return $oResponse;
		}

		//Check for category validity
		$aInvalidCategories = array();
		foreach ( $aCategories as $sCategoryName ) {
			if ( Category::newFromName( $sCategoryName ) === false ) {
				$aInvalidCategories[] = $sCategoryName;
			}
		}

		if( !empty( $aInvalidCategories ) ) {
			$iCount = count( $aInvalidCategories );
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-categories-not-valid',
				implode( ', ', $aInvalidCategories ),
				$iCount
			)->text();
			$oResponse->payload = $aInvalidCategories;
			$oResponse->payload_count = $iCount;
			return $oResponse;
		}

		$oWikiPage = WikiPage::factory( $oTitle );
		if ( $oWikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ){
			$oContent = $oWikiPage->getContent();
			$sText = '';
			if( $oContent instanceof Content ) {
				$sText = $oContent->getNativeData();
			}
		}
		else {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->plain();
			return $oResponse;
		}

		// Remove all category links before adding the new ones
		$sCanonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
		$sLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		$sPattern = "#\[\[($sLocalNSName|$sCanonicalNSName):.*?\]\]#si";
		$sText = preg_replace( $sPattern, '', $sText );

		foreach ( $aCategories as $sCategoryName ) {
			$sText .= "\n[[" . $sLocalNSName . ":$sCategoryName]]";
		}
		$oContent = ContentHandler::makeContent( $sText, $oTitle );
		$oStatus = $oWikiPage->doEditContent(
			$oContent,
			wfMessage( 'bs-wikipage-tasks-setcategories-edit-summary' )->plain()
		);

		if ( !$oStatus->isGood() ) {
			$oResponse->message = $oStatus->getMessage();
		}
		else {
			$oUpdates = $oContent->getSecondaryDataUpdates( $oWikiPage->getTitle() );
			DataUpdate::runUpdates( $oUpdates );
			$oResponse->success = true;
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-setcategories-success' )->plain();
		}

		return $oResponse;
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_getExplicitCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$oTitle = $oTitle = $this->getTitleFromTaskData( $oTaskData );

		if ( !$oTitle->userCan( 'read' ) ) {
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-page-read-not-allowed',
				$oTitle->getPrefixedText()
			)->plain();
			return $oResponse;
		}

		//get page and content
		$oWikiPage = WikiPage::factory( $oTitle );
		if ( $oWikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ){
			$oContent = $oWikiPage->getContent();
			$sText = '';
			if( $oContent instanceof Content ) {
				$sText = $oContent->getNativeData();
			}

		}
		else {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->plain();
			return $oResponse;
		}

		//Pattern for Category tags
		$sCanonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
		$sLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		$sPattern = "#\[\[($sLocalNSName|$sCanonicalNSName):(.*?)\]\]#si";
		$matches = [];
		$matchCount = preg_match_all($sPattern, $sText, $matches, PREG_PATTERN_ORDER);

		$aCategories = [];
		//normalize
		foreach ( $matches[2] as $match ){
			$oCategoryTitle = Title::newFromText( $match, NS_CATEGORY );
			array_push( $aCategories, $oCategoryTitle->getText() );
		}

		$oResponse->success = true;
		$oResponse->payload = $aCategories;
		$oResponse->payload_count = $matchCount;

		return $oResponse;
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_addCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();

		$oCategoriesInPage = $this->task_getExplicitCategories($oTaskData, $aParams);

		$aCategories = $oTaskData->categories;
		$aCategories = ( !is_array( $aCategories ) ) ? array() : $aCategories;

		$oTitle = $this->getTitleFromTaskData( $oTaskData );

		if ( !$oTitle->userCan( 'edit' ) ) {
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-page-edit-not-allowed',
				$oTitle->getPrefixedText()
			)->plain();
			return $oResponse;
		}

		//Check for category validity
		$aInvalidCategories = array();
		foreach ( $aCategories as $sCategoryName ) {
			if ( Category::newFromName( $sCategoryName ) === false ) {
				$aInvalidCategories[] = $sCategoryName;
			}
		}

		if( !empty( $aInvalidCategories ) ) {
			$iCount = count( $aInvalidCategories );
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-categories-not-valid',
				implode( ', ', $aInvalidCategories ),
				$iCount
			)->text();
			$oResponse->payload = $aInvalidCategories;
			$oResponse->payload_count = $iCount;
			return $oResponse;
		}

		if ($oCategoriesInPage->payload_count > 0){
			$aNewCategories = array_diff($aCategories, $oCategoriesInPage->payload);
		} else {
			$aNewCategories = $aCategories;
		}

		$oWikiPage = WikiPage::factory( $oTitle );
		if ( $oWikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ){
			$oContent = $oWikiPage->getContent();
			$sText = '';
			if( $oContent instanceof Content ) {
				$sText = $oContent->getNativeData();
			}

		}
		else {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->plain();
			return $oResponse;
		}

		$sLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		foreach ( $aNewCategories as $sCategoryToAdd ) {
			$sText .= "\n[[" . $sLocalNSName . ":".$sCategoryToAdd."]]";
		}

		$oContent = ContentHandler::makeContent( $sText, $oTitle );
		$oStatus = $oWikiPage->doEditContent(
			$oContent,
			wfMessage( 'bs-wikipage-tasks-setcategories-edit-summary' )->plain()
		);

		if ( !$oStatus->isGood() ) {
			$oResponse->message = $oStatus->getMessage();
		}
		else {
			$oUpdates = $oContent->getSecondaryDataUpdates( $oWikiPage->getTitle() );
			DataUpdate::runUpdates( $oUpdates );
			$oResponse->success = true;
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-setcategories-success' )->plain();
			$oResponse->payload = $this->makeCategoryTaskPayload( $oTitle->getArticleID() );
		}

		return $oResponse;
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_removeCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$aCategoriesToRemove = $oTaskData->categories;

		if (count($aCategoriesToRemove) === 0){
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-nothingtoremove')->plain();
			$oResponse->payload = array();
			$oResponse->payload_count = 0;
			return $oResponse;
		}

		$oTitle = $this->getTitleFromTaskData( $oTaskData );

		if ( !$oTitle->userCan( 'edit' ) ) {
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-page-edit-not-allowed',
				$oTitle->getPrefixedText()
			)->plain();
			return $oResponse;
		}

		//get page and content
		$oWikiPage = WikiPage::factory( $oTitle );
		if ( $oWikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ){
			$oContent = $oWikiPage->getContent();
			$sText = '';
			if( $oContent instanceof Content ) {
				$sText = $oContent->getNativeData();
			}

		}
		else {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->plain();
			return $oResponse;
		}

		$sCanonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
		$sLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		foreach ($aCategoriesToRemove as $sToRemove){
			$linksToRemove = $this->findCategoryLinksInText( $sToRemove, $sText );
			foreach( $linksToRemove as $linkToRemove ) {
				$sText = str_replace( $linkToRemove, '', $sText );
			}
		}
		//TODO: remove blank lines from page
		$oContent = ContentHandler::makeContent( $sText, $oTitle );
		$oStatus = $oWikiPage->doEditContent(
			$oContent,
			wfMessage( 'bs-wikipage-tasks-setcategories-edit-summary' )->plain()
		);

		if ( !$oStatus->isGood() ) {
			$oResponse->message = $oStatus->getMessage();
		}
		else {
			$oUpdates = $oContent->getSecondaryDataUpdates( $oWikiPage->getTitle() );
			DataUpdate::runUpdates( $oUpdates );
			$oResponse->success = true;
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-setcategories-success' )->plain();
		}

		return $oResponse;
	}

	/**
	 * Parses all internal links on page into Title objects
	 * and compares to the category title we need.
	 *
	 * @param string $category
	 * @param string $text
	 * @return array Links texts for requested category
	 */
	protected function findCategoryLinksInText( $category,$text ) {
		$categoryTitle = Title::makeTitle( NS_CATEGORY, $category );
		if( $categoryTitle instanceof Title === false ) {
			return [];
		}

		$categoryLinkText = [];
		$internalLinks = [];
		preg_match_all( '#\[\[(.*?)\]\]#si', $text, $internalLinks );

		if( !isset( $internalLinks[1] ) && count( $internalLinks[1] ) === 0 ) {
			return [];
		}
		foreach( $internalLinks[1] as $key => $pageName ) {
			if( strpos( '|', $pageName ) !== false ) {
				$pageName = explode( '|', $pageName )[0];
			}
			$titleToTest = \Title::newFromText( $pageName );
			if( $titleToTest instanceof \Title === false ) {
				continue;
			}
			if( $categoryTitle->equals( $titleToTest ) ) {
				$categoryLinkText[] = $internalLinks[0][$key];
			}
		}

		return $categoryLinkText;
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_getDiscussionCount( $oTaskData, $aParams  ) {
		$oResponse = $this->makeStandardReturn();

		$iCount = BsArticleHelper::getInstance(
			$this->getTitleFromTaskData( $oTaskData )
		)->getDiscussionAmount();

		$oResponse->success = true;
		$oResponse->payload = $iCount ;

		return $oResponse;
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @return Title
	 * @throws MWException
	 * @todo: Maybe have this logic in "parent::getTitle" altogether
	 */
	protected function getTitleFromTaskData( $oTaskData ) {
		$oTitle = null;
		if ( isset( $oTaskData->page_id ) ) {
			$oTitle = Title::newFromID( $oTaskData->page_id );
		}
		if ( $oTitle instanceof Title === false && isset( $oTaskData->page_title ) ) {
			$oTitle = Title::newFromText( $oTaskData->page_title );
		}
		if ( $oTitle instanceof Title === false ) {
			$oTitle = $this->getTitle();
		}

		//Actually this should never happen as $this->getTitle() will at least
		//return title "Special:BadTitle"
		if ( $oTitle instanceof Title === false ) {
			throw new MWException(
				wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->plain()
			);
		}

		return $oTitle;
	}

	protected function makeCategoryTaskPayload( $pageId ) {
		$oTitle = Title::newFromID( $pageId );
		$result = $this->task_getExplicitCategories( (object)[ 'page_id' => $pageId ], [] );
		return array(
			'page_id' => $oTitle->getArticleID(),
			'page_prefixed_text' => $oTitle->getPrefixedText(),
			'categories' => $result->payload
		);
	}

	/**
	 *
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return BSStandardAPIResponse
	 */
	protected function task_getTemplateTree( $oTaskData, $aParams  ) {
		$oResponse = $this->makeStandardReturn();

		$oTitle = $this->getTitleFromTaskData( $oTaskData );
		$oWikiPage = WikiPage::factory( $oTitle );
		$oContent = $oWikiPage->getContent();
		if( $oContent instanceof WikitextContent === false ) {
			$oResponse->message =
				wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->plain();
			return $oResponse;
		}

		$sWikiText = $oContent->getNativeData();
		$oTemplateTreeParser =
			new BlueSpice\Utility\WikiTextTemplateTreeParser( $sWikiText );

		$oResponse->success = true;
		$oResponse->payload = $oTemplateTreeParser->getArray();

		return $oResponse;
	}
}
