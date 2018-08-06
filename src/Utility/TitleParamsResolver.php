<?php

namespace BlueSpice\Utility;

/**
 * A lot of MediaWiki (Web)APIs accept a title context information (e.g.
 * ApiParse)
 * - "pageid=233"
 * - "title=Some page"
 * - "page=Some page"
 * - "target=Some page"
 *
 * Some even lists of titles (e.g. ApiPurge)
 * - "pageids=233|453|2843"
 * - "titles=First|Second|Third"
 *
 * Then there are some that accept one or more revision ids (e.g. ApiReview)
 * - "revid=3424"
 * - "revids=34234|67556|435"
 * - "oldid=3647"
 * - "previd=3898"
 * - "baserevid=7487"
 *
 * Last but not least there are BlueSpice TaskAPIs that provide things like
 * - "page_id:324"
 * - "pid:324"
 * - "page_prefixedtitle:'Help:Some_page'"
 * - "page_title:'Some_page',page_namespace:12"
 */
class TitleParamsResolver {

	/**
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 *
	 * @var \Title[]
	 */
	protected $titles = null;

	/**
	 *
	 * @var \Title
	 */
	protected $default = null;

	/**
	 *
	 * @param array $params
	 * @params \Title|null $default
	 */
	public function __construct( $params, $default = null ) {
		$this->params = $params;
		$this->default = $default;

		if( $this->default === null ) {
			//Question: Better be "\Title::newMainPage()"?
			$this->default = \Title::makeTitle( NS_SPECIAL, 'Badtitle/dummy title' );
		}
	}

	/**
	 *
	 * @return \Title[] all titles that could be found in the provided params
	 */
	public function resolve() {
		foreach( $this->params as $paramName => $paramValue ) {
			$this->resolvePageIds( $paramName, $paramValue );
			$this->resolvePageNames( $paramName, $paramValue );
			$this->resolveRevisionIds( $paramName, $paramValue );
		}

		return $this->getResultOrDefault();
	}

	/**
	 *
	 * @return \Title[]
	 */
	protected function getResultOrDefault() {
		if( empty( $this->titles ) ) {
			return [ $this->default ];
		}
		return array_values( $this->titles );
	}

	protected function resolvePageIds( $paramName, $paramValue ) {
		$pageIds = [];
		if( $this->isPageIdParam( $paramName ) ) {
			$pageIds = [ $paramValue ];
		}
		else if( $this->isPageIdsParam( $paramName ) ) {
			$pageIds = explode( '|', $paramValue );
		}

		foreach( $pageIds as $pageId ) {
			$title = \Title::newFromID( $pageId );
			if( $title instanceof \Title ) {
				$this->titles[$title->getPrefixedDBkey()] = $title;
			}
		}
	}

	protected function isPageIdParam( $paramName ) {
		return in_array( $paramName, [ 'pageid', 'page_id', 'pid' ] );
	}

	protected function isPageIdsParam( $paramName ) {
		return in_array( $paramName, [ 'pageids' ] );
	}

	protected function resolvePageNames( $paramName, $paramValue ) {
		$pageNames = [];
		if( $this->isPageNameParam( $paramName ) ) {
			$pageNames = [ $paramValue ];
		}
		else if( $this->isPageNamesParam( $paramName ) ) {
			$pageNames = explode( '|', $paramValue );
		}

		foreach( $pageNames as $pageName ) {
			$title = \Title::newFromText( $pageName );
			if( $title instanceof  \Title ) {
				$this->titles[$title->getPrefixedDBkey()] = $title;
			}
		}
	}

	protected function isPageNameParam( $paramName ) {
		return in_array( $paramName, [ 'title', 'page', 'target' ] );
	}

	protected function isPageNamesParam( $paramName ) {
		return in_array( $paramName, [ 'titles' ] );
	}

	protected function resolveRevisionIds( $paramName, $paramValue ) {
		$revisionIds = [];
		if( $this->isRevisionIdParam( $paramName ) ) {
			$revisionIds = [ $paramValue ];
		}
		else if( $this->isRevisionIdsParam( $paramName ) ) {
			$revisionIds = explode( '|', $paramValue );
		}

		foreach( $revisionIds as $revId ) {
			$revision = \Revision::newFromId( $revId );
			if( $revision instanceof \Revision ) {
				$title = $revision->getTitle();
				if( $title instanceof  \Title ) {
					$this->titles[$title->getPrefixedDBkey()] = $title;
				}
			}
		}
	}

	protected function isRevisionIdParam( $paramName ) {
		return in_array( $paramName, [ 'revid', 'oldid', 'previd', 'baserevid' ] );
	}

	protected function isRevisionIdsParam( $paramName ) {
		return in_array( $paramName, [ 'revids' ] );
	}

}
