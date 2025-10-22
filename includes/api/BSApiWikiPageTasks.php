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
 * For further information visit https://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

use BlueSpice\Api\Task;
use BlueSpice\Utility\WikiTextLinksHelper\CategoryLinksHelper;
use MediaWiki\Api\ApiMain;
use MediaWiki\Content\TextContent;
use MediaWiki\Content\WikitextContent;
use MediaWiki\Json\FormatJson;
use MediaWiki\Request\FauxRequest;
use MediaWiki\Title\Title;

/**
 * Provides common tasks that can be performed on a WikiPage
 * @package BlueSpice_Foundation
 */
class BSApiWikiPageTasks extends BSApiTasksBase {
	/**
	 *
	 * @var array
	 */
	protected $aTasks = [
		'setCategories' => [
			// 'permissions' => [], //TODO migrate "getRequiredTaskPermissions"
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
			// 'readonly' => true, //TODO migrate "$this->aReadTasks"
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
	];

	/**
	 * @var string[]
	 */
	protected $aReadTasks = [
		'getDiscussionCount',
		'getExplicitCategories',
		'getTemplateTree'
	];

	/**
	 * Configures the global permission requirements
	 *
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return [
			'setCategories' => [ 'edit' ],
			'getExplicitCategories' => [ 'read' ],
			'addCategories' => [ 'edit' ],
			'removeCategories' => [ 'edit' ],
			'getDiscussionCount' => [ 'read' ],
			'getTemplateTree' => [ 'read' ]
		];
	}

	/**
	 * @param \stdClass $taskData
	 * @param \stdClass $response
	 * @param string $task
	 * @return \stdClass
	 */
	protected function legacyWikiPageTaskCategory( \stdClass $taskData, $response, $task ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );

		$title = $this->getTitleFromTaskData( $taskData );

		$rawContext = $this->getParameter( Task::PARAM_CONTEXT );
		if ( $title instanceof Title ) {
			$rawContext->wgArticleId = 0;
			if ( $title->exists() ) {
				$rawContext->wgArticleId = $title->getArticleID();
			}
			$rawContext->wgNamespaceNumber = $title->getNamespace();
			$rawContext->wgPageName = $title->getPrefixedText();
			$rawContext->wgRelevantPageName = $title->getPrefixedText();
			$rawContext->wgTitle = $title->getText();
			$rawContext->wgCanonicalNamespace = $this->services->getNamespaceInfo()
				->getCanonicalName( $title->getNamespace() );
			$rawContext->wgCanonicalSpecialPageName = false;
		}

		$req = new FauxRequest( array_merge(
			[ Task::PARAM_TASK_DATA => FormatJson::encode( $taskData ) ],
			[ Task::PARAM_CONTEXT => FormatJson::encode( $rawContext ) ],
			[ 'action' => 'bs-task', Task::PARAM_TASK => $task ]
		) );
		$api = new ApiMain( $req, true );
		$api->execute();
		foreach ( [ 'message', 'errors', 'payload', 'payload_count', 'success' ] as $path ) {
			if ( isset( $api->getResult()->getResultData()[$path] ) ) {
				$response->{$path} = $api->getResult()->getResultData()[$path];
			}
		}
		return $response;
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - use new task api with task
	 * 'wikipage-setcategories'
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_setCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$oTaskData->categories = !is_array( $oTaskData->categories )
			? []
			: $oTaskData->categories;
		$oTaskData->categories = implode( '|', $oTaskData->categories );
		if ( empty( $oTaskData->categories ) ) {
			unset( $oTaskData->categories );
		}

		return $this->legacyWikiPageTaskCategory(
			$oTaskData,
			$oResponse,
			'wikipage-setcategories'
		);
	}

	/**
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_getExplicitCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$title = $this->getTitleFromTaskData( $oTaskData );

		if ( !$this->services->getPermissionManager()
			->userCan( 'read', $this->getUser(), $title )
		) {
			$oResponse->message = wfMessage(
				'bs-wikipage-tasks-error-page-read-not-allowed',
				$title->getPrefixedText()
			)->text();
			return $oResponse;
		}

		// get page and content
		$oWikiPage = $this->services->getWikiPageFactory()->newFromTitle( $title );
		if ( $oWikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ) {
			$content = $oWikiPage->getContent();
			$wikitext = ( $content instanceof TextContent ) ? $content->getText() : '';

		} else {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->text();
			return $oResponse;
		}

		$categoryLinksHelper = new CategoryLinksHelper( $wikitext );

		$categoriesList = $categoryLinksHelper->getExplicitCategories();

		$oResponse->success = true;
		$oResponse->payload = $categoriesList;
		$oResponse->payload_count = count( $categoriesList );

		return $oResponse;
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - use new task api with task
	 * 'wikipage-addcategories'
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_addCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$oTaskData->categories = !is_array( $oTaskData->categories )
			? []
			: $oTaskData->categories;
		$oTaskData->categories = implode( '|', $oTaskData->categories );
		if ( empty( $oTaskData->categories ) ) {
			unset( $oTaskData->categories );
		}

		return $this->legacyWikiPageTaskCategory(
			$oTaskData,
			$oResponse,
			'wikipage-addcategories'
		);
	}

	/**
	 * DEPRECATED
	 * @deprecated since version 3.1 - use new task api with task
	 * 'wikipage-removecategories'
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_removeCategories( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();
		$oTaskData->categories = !is_array( $oTaskData->categories )
			? []
			: $oTaskData->categories;
		$oTaskData->categories = implode( '|', $oTaskData->categories );
		if ( empty( $oTaskData->categories ) ) {
			unset( $oTaskData->categories );
		}

		return $this->legacyWikiPageTaskCategory(
			$oTaskData,
			$oResponse,
			'wikipage-removecategories'
		);
	}

	/**
	 * @deprecated since version 3.1 - Not in use anymore
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_getDiscussionCount( $oTaskData, $aParams ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$oResponse = $this->makeStandardReturn();

		$iCount = BsArticleHelper::getInstance(
			$this->getTitleFromTaskData( $oTaskData )
		)->getDiscussionAmount();

		$oResponse->success = true;
		$oResponse->payload = $iCount;

		return $oResponse;
	}

	/**
	 * @param stdClass $oTaskData
	 * @return Title
	 * @throws MWException
	 * @todo Maybe have this logic in "parent::getTitle" altogether
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

		// Actually this should never happen as $this->getTitle() will at least
		// return title "Special:BadTitle"
		if ( $oTitle instanceof Title === false ) {
			throw new MWException(
				wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->text()
			);
		}

		return $oTitle;
	}

	/**
	 * @param int $pageId
	 * @return array
	 */
	protected function makeCategoryTaskPayload( $pageId ) {
		$oTitle = Title::newFromID( $pageId );
		$result = $this->task_getExplicitCategories( (object)[ 'page_id' => $pageId ], [] );
		return [
			'page_id' => $oTitle->getArticleID(),
			'page_prefixed_text' => $oTitle->getPrefixedText(),
			'categories' => $result->payload
		];
	}

	/**
	 * @param stdClass $oTaskData
	 * @param array $aParams
	 * @return \BlueSpice\Api\Response\Standard
	 */
	protected function task_getTemplateTree( $oTaskData, $aParams ) {
		$oResponse = $this->makeStandardReturn();

		$oTitle = $this->getTitleFromTaskData( $oTaskData );
		$oWikiPage = $this->services->getWikiPageFactory()->newFromTitle( $oTitle );
		$content = $oWikiPage->getContent();
		if ( $content instanceof WikitextContent === false ) {
			$oResponse->message =
				wfMessage( 'bs-wikipage-tasks-error-contentmodel' )->text();
			return $oResponse;
		}

		$wikiText = ( $content instanceof TextContent ) ? $content->getText() : '';
		$oTemplateTreeParser =
			new BlueSpice\Utility\WikiTextTemplateTreeParser( $wikiText );

		$oResponse->success = true;
		$oResponse->payload = $oTemplateTreeParser->getArray();

		return $oResponse;
	}
}
