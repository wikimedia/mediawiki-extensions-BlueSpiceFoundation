<?php
/**
 * Provides common tasks that can pe performed on a WikiPage.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Provides common tasks that can pe performed on a WikiPage
 * @package BlueSpice_Foundation
 */
class BSApiWikiPageTasks extends BSApiTasksBase {
	protected $aTasks = array(
		'setCategories',
		'getExplicitCategories',
		'addCategories',
		'removeCategories'
	);

	/**
	 * Configures the global permission requirements
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return array(
			'setCategories' => array( 'edit' ),
			'getExplicitCategories' => array( 'read' ),
			'addCategories' => array( 'edit' ),
			'removeCategories' => array( 'edit' )
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

		$oTitle = Title::newFromText( $oTaskData->page_title );

		if ( $oTitle instanceof Title === false ) {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->plain();
			return $oResponse;
		}

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
		//get Title of the page
		$oTitle = Title::newFromText( $oTaskData->page_title );

		if ( $oTitle instanceof Title === false ) {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->plain();
			return $oResponse;
		}

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

		//Patern for Category tags
		$sCanonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
		$sLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
		$sPattern = "#\[\[($sLocalNSName|$sCanonicalNSName):(.*?)\]\]#si";
		$matches = [];
		$matchCount = 0;
		$matchCount = preg_match_all($sPattern, $sText, $matches, PREG_PATTERN_ORDER);

		$aCategories = [];
		//normalize
		foreach ($matches[2] as $match){
			$sMatch = preg_replace('!_+!', ' ', $match);
			$sMatch = preg_replace('!\s+!', ' ', $sMatch);
			array_push($aCategories, $sMatch);
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

		$oTitle = Title::newFromText( $oTaskData->page_title );

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

		//get Title of the page
		$oTitle = Title::newFromText( $oTaskData->page_title );
		if ( $oTitle instanceof Title === false ) {
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->plain();
			return $oResponse;
		}

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
			$sPattern = "#\[\[($sLocalNSName|$sCanonicalNSName):($sToRemove)\]\]#si";
			$sText = preg_replace($sPattern, '', $sText);
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
			$oResponse->success = true;
			$oResponse->message = wfMessage( 'bs-wikipage-tasks-setcategories-success' )->plain();
		}

		return $oResponse;
	}

	public function needsToken() {
		return parent::needsToken();
	}
}
