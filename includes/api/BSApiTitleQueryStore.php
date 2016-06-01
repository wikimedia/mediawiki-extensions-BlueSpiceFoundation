<?php

//api.php?action=bs-titlequery-store&format=jsonfm&options={%22namespaces%22:[-1,0,2,4,6,8,10,12,14,3000],%20%22returnQuery%22:true}&query=Date

class BSApiTitleQueryStore extends BSApiExtJSStoreBase {

	/**
	 * Returns a List of Titles for the client side
	 * @param string $sQuery A (maybe prefixed) title, or parts of a title
	 * that the store should look for
	 * @global Language $wgContLang
	 * @return array of objects
	 */
	protected function makeData( $sQuery = '' ) {
		global $wgContLang;

		$aOptions = $this->getParameter( 'options' ) + array(
			'limit' => 100,
			'namespaces' => array(),
			'returnQuery' => false,
			'returnTypes' => array(
				'wikipage',
				#'specialpage',
				#'namespace'
			)
		);

		$sNormQuery = strtolower( $sQuery );
		$sNormQuery = str_replace( '_', ' ', $sNormQuery );

		//See JS BS.model.Title
		$aData = array();
		$aDataSet = array(
			'page_id' => 0,
			'page_namespace' => 0,
			'page_title' => '',
			'prefixedText' => '',
			'displayText' => '',
			'type' => 'wikipage'
		);

		//Step 1: Collect namespaces
		$aNamespaces = $wgContLang->getNamespaces();
		asort( $aNamespaces );
		foreach ( $aNamespaces as $iNsId => $sNamespaceText ) {
			if ( empty( $sNamespaceText ) ) {
				continue;
			}

			if ( !in_array( $iNsId, $aOptions['namespaces'] ) ) {
				continue;
			}

			$sNormNSText = strtolower( $sNamespaceText );
			$sNormNSText = str_replace( '_', ' ', $sNormNSText );

			if ( empty( $sNormQuery ) || strpos( $sNormNSText, $sNormQuery ) === 0) {

				//Only namespaces a user has the read permission for
				$oDummyTitle = Title::newFromText( $sNamespaceText.':X' );
				if ( $oDummyTitle->userCan( 'read' ) === false ) {
					continue;
				}

				$aData[] = (object)(array(
					'type' => 'namespace',
					'displayText' => $sNamespaceText.':'
				) + $aDataSet);
			}
		}

		if ( empty( $sNormQuery ) ) {
			return $aData;
		}

		//Step 2: Find pages
		$oQueryTitle = Title::newFromText( $sQuery );

		if ( $oQueryTitle instanceof Title === false ) {
			return $aData;
		}

		//This is an ugly workaround to achieve a case insensitive lookup of
		//page titles. Even though the 'searchindex.si_title' column saves a
		//lower cased version of the title the standard MediaWiki APIs
		//(SearchEngine, *PrefixSearch, API modules 'search' and
		//'prefixsearch') do only case sensitive lookups. A good solution could
		//be to use the TitleKey extension by Brion Vibber
		//(https://www.mediawiki.org/wiki/Extension:TitleKey) to have a
		//consistent case insensitive search behavior.
		//The current approach has a major disadvantage: It does not find
		//anything when the query contains a hyphen! Also be careful
		//the PrefixSearch returns SpecialPages with their aliases.

		$dbr = $this->getDB();

		// We want LIKE operator behind every term,
		// so multi term queries also bring results
		$sOp = $dbr->anyString();
		$aLike = array( '', $sOp );
		$aParams = explode( ' ', $oQueryTitle->getText() );
		$oSearchEngine = SearchEngine::create();
		foreach ( $aParams as $sParam ) {
			$aLike[] = $oSearchEngine->normalizeText( $sParam );
			$aLike[] = $sOp;
		}

		$aConditions = array(
			'page_id = si_page',
			'si_title '. $dbr->buildLike( $aLike ),
		);

		if ( $oQueryTitle->getNamespace() !== NS_MAIN || strpos( $sQuery, ':' ) === 0 ) {
			$aConditions['page_namespace'] = $oQueryTitle->getNamespace();
		}

		$res = $dbr->select(
			array( 'page', 'searchindex' ),
			array( 'page_id' ),
			$aConditions,
			__METHOD__,
			array(
				'LIMIT' => $aOptions['limit']
			)
		);

		$aTitles = array();
		foreach ( $res as $row ) {
			$oTitle = Title::newFromID( $row->page_id );
			if ( $oTitle->userCan( 'read' ) === false ) continue;

			$aTitles[] = $oTitle;
		}

		if ( $aOptions['returnQuery'] === true ) {
			//We prepend the query title to the list of titles
			array_unshift( $aTitles, $oQueryTitle );
		}

		foreach ( $aTitles as $oTitle ) {
			//If we return the query itself we have to filter out a potential
			//match found by the search
			if ( $aOptions['returnQuery'] === true ) {
				if ( $oQueryTitle !== $oTitle && $oQueryTitle->equals( $oTitle ) ) {
					continue;
				}
			}

			$sPrefixedText = $oTitle->getPrefixedText();
			$aData[] = (object)(array(
				'type' => 'wikipage',
				'page_id' => $oTitle->getArticleId(),
				'page_namespace' => $oTitle->getNamespace(),
				'page_title' => $oTitle->getText(),
				'prefixedText' => $sPrefixedText,
				'displayText' => $sPrefixedText
			) + $aDataSet);
		}

		//Step 3: Find Specialpages
		//Add specialpages that are not held in the database.
		//This needs some more thinking: At the moment we calculate both a
		//"prefixedText" that can be used for linking and URLs in general - and
		//a "displayText" that the user sees. This is because the average
		//SpecialPage name looks pretty unfamiliar to a user, because on
		//Special:Specialpages and in the SpecialPages themselves a
		//"description" text is used. It is especially true for English
		//language.
		//In means of a "Title" the canonical names would be better, because you
		//cannot link or access a SpecialPage by its description
		$aSpecialPages = SpecialPageFactory::getList();
		$aSPDataSets = array();
		$aSortHelper = array();
		$aClassNames = array();
		foreach ( $aSpecialPages as $sSpecialPageName => $sClassName ) {

			//Prevent double listing
			if ( in_array( $sClassName, $aClassNames ) ) {
				continue;
			}

			$aClassNames[] = $sClassName;

			$oSpecialPage = SpecialPageFactory::getPage($sSpecialPageName);
			if ( !( $oSpecialPage instanceof SpecialPage ) ){
				wfDebug( __METHOD__.': "'.$sSpecialPageName.'" is not a valid SpecialPage' );
				continue;
			}

			//This seems awkward. There has to be a better way...
			$sMsgKey = strtolower( $sSpecialPageName );
			$sSPDisplayText = Title::makeTitle(
				NS_SPECIAL, wfMessage( $sMsgKey )->inContentLanguage()->plain()
			)->getPrefixedText();

			if ( strpos( strtolower( $sSPDisplayText ), $sNormQuery ) !== 0 ) {
				continue;
			}

			if ( $oSpecialPage->isListed() == false ) {
				continue;
			}

			//Filter out SpecialPages that the current user may not execute
			if ( !$oSpecialPage->userCanExecute( $this->getUser() ) ) {
				continue;
			}

			$sSPText = $oSpecialPage->getPageTitle()->getPrefixedText();

			$aSPDataSets[] = (object)(array(
				'type' => 'specialpage',
				'prefixedText' => $sSPText,
				'displayText' => $sSPDisplayText
			) + $aDataSet);

			$aSortHelper[] = $sSPDisplayText;
		}

		//We want the result to be sorted by its display text!
		array_multisort( $aSortHelper, SORT_NATURAL, $aSPDataSets );

		$aData += $aSPDataSets;
		return $aData;
	}

	public function getAllowedParams() {
		$aParams = parent::getAllowedParams();
		$aParams['options'] = array(
			ApiBase::PARAM_TYPE => 'string',
			ApiBase::PARAM_REQUIRED => false,
			ApiBase::PARAM_DFLT => '{}',
			10 /*ApiBase::PARAM_HELP_MSG*/ => 'apihelp-bs-store-param-options',
		);
		return $aParams;
	}

	protected function getParameterFromSettings( $paramName, $paramSettings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $paramName, $paramSettings, $parseLimit );
		//Unfortunately there is no way to register custom types for parameters
		if ( $paramName == 'options' ) {
			$value = FormatJson::decode( $value, true );
			if ( $value === null ) {
				return array();
			}
		}
		return $value;
	}
}