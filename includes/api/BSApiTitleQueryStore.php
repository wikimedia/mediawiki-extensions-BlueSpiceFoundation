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
			'limit' => 250,
			'namespaces' => array(),
			'returnQuery' => false
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

		if( empty( $aOptions['namespaces'] ) ) {
			//Search in all namespaces by default
			$aOptions['namespaces'] = $wgContLang->getNamespaceIds();
			if( !in_array( NS_MAIN, $aOptions['namespaces'] ) ) {
				//Add main namespace!
				$aOptions['namespaces'][] = NS_MAIN;
			}
		} else {
			//validate user input
			foreach( $aOptions['namespaces'] as $iKey => $iNSId ) {
				if( !$iNSId && !is_numeric( $iNSId ) ) {
					unset( $aOptions['namespaces'][$iKey] );
					continue;
				}
				if( !in_array( $iNSId, $wgContLang->getNamespaceIds() ) ) {
					//Namespace index does not exist
					unset( $aOptions['namespaces'][$iKey] );
					continue;
				}
				$aOptions['namespaces'][$iKey] = (int) $iNSId;
			}
		}

		//Step 1: Collect namespaces
		$aNamespaces = $wgContLang->getNamespaces();
		$aNsCondition = [];
		if( in_array( 0, $aOptions['namespaces'] ) ) {
			//if main space is allowed, hand it over directly to the query
			$aNsCondition[] = 0;
		}
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
			$sNormNSText = "$sNormNSText:";

			//Only namespaces a user has the read permission for
			$oDummyTitle = Title::newFromText( $sNamespaceText.':X' );
			if ( $oDummyTitle->userCan( 'read' ) === false ) {
				continue;
			}
			$aNsCondition[] = $iNsId;
			if ( empty( $sNormQuery ) || strpos( $sNormNSText, $sNormQuery ) === 0) {
				$aData[] = (object)(array(
					'type' => 'namespace',
					'prefixedText' => $sNamespaceText.':',
					'displayText' => $sNamespaceText.':'
				) + $aDataSet);
			}
		}

		if ( empty( $sNormQuery ) ) {
			return $aData;
		}
		//rare case of absolutely no permission or only not existing namespaces
		//in options['namespaces']
		if( empty( $aNsCondition ) ) {
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
		$aLike = $aNormalLike = array( '', $sOp );
		$aParams = explode( ' ', str_replace( '/', ' ', $oQueryTitle->getText() ) );
		$oSearchEngine = SearchEngine::create();
		foreach ( $aParams as $sParam ) {
			$aLike[] = $sParam;
			$aLike[] = $sOp;
			$aNormalLike[] = $oSearchEngine->normalizeText( $sParam );
			$aNormalLike[] = $sOp;
		}

		$aConditions = array(
			'page_id = si_page',
			'si_title '. $dbr->buildLike( $aLike ) . ' OR si_title ' . $dbr->buildLike( $aNormalLike ),
		);

		$aConditions['page_namespace'] = $aNsCondition;
		if( $oQueryTitle->getNamespace() !== NS_MAIN || strpos( $sNormQuery, ':' ) === 0 ) {
			$aConditions['page_namespace'] = $oQueryTitle->getNamespace();
		}
		if( $oQueryTitle->getNamespace() === NS_MEDIA ) {
			$aConditions['page_namespace'] = NS_FILE;
		}

		$res = $dbr->select(
			array( 'page', 'searchindex' ),
			array( 'page_id', 'page_title' ),
			$aConditions,
			__METHOD__,
			array(
				'LIMIT' => $aOptions['limit']
			)
		);

		$aTitles = array();
		foreach ( $res as $row ) {
			if( $oQueryTitle->getNamespace() === NS_MEDIA ) {
				$oTitle = Title::newFromText( $row->page_title, NS_MEDIA );
			} else {
				$oTitle = Title::newFromID( $row->page_id );
			}
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

			$pagetype = 'wikipage';

			if ( $sPrefixedText === ucfirst( $sQuery ) || $sPrefixedText === $sQuery ) {
				$pagetype = 'directsearch';
			}

			$aData[] = (object)(array(
				'type' => $pagetype,
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
		if ( !in_array( NS_SPECIAL, $aOptions['namespaces'] ) ) {
			return $aData;
		}
		$aSpecialPages = SpecialPageFactory::getNames();
		$aSPDataSets = array();
		$sSpecialNmspPrefix = $this->getLanguage()->getNsText( NS_SPECIAL );

		$normQueryParts = explode( ':', $sNormQuery, 2 );
		$sUnprefixedNormQuery = array_pop( $normQueryParts );

		foreach ( $aSpecialPages as $sSpecialPageName ) {

			$oSpecialPage = SpecialPageFactory::getPage( $sSpecialPageName );
			if ( !( $oSpecialPage instanceof SpecialPage ) ){
				wfDebug( __METHOD__ . ': "' . $sSpecialPageName . '" is not a valid SpecialPage' );
				continue;
			}
			$sSPDisplayText = $oSpecialPage->getDescription();

			$sQueryPos = strpos(
				strtolower( $sSPDisplayText ),
				$sUnprefixedNormQuery
			);


			if ( $sQueryPos === false ) {
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
				'page_id' => 0,
				'page_namespace' => NS_SPECIAL,
				'page_title' => $sSpecialPageName,
				'prefixedText' => $sSPText,
				'displayText' => $sSpecialNmspPrefix . ':' .  $sSPDisplayText
			) + $aDataSet);

		}

		$aData = array_merge( $aData, $aSPDataSets );

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

	// Sorting does not work here, so skip it
	public function sortData( $aProcessedData ) {
		return $aProcessedData;
	}

}
