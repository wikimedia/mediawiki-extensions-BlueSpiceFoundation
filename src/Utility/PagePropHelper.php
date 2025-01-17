<?php
namespace BlueSpice\Utility;

use BlueSpice\TargetCache\Title\Target;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;

/**
 * @deprecated since 4.3 Use MediaWiki core `\PageProps` instead
 */
class PagePropHelper {

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $services;

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 * @param MediaWikiServices $services
	 * @param Title $title
	 */
	public function __construct( $services, Title $title ) {
		$this->title = $title;
		$this->services = $services;
	}

	/**
	 *
	 * @param string $name
	 * @param mixed|null $default
	 * @return string|$default
	 * @deprecated since 4.3 Use MediaWiki core `\PageProps` instead
	 */
	public function getPageProp( $name, $default = null ) {
		wfDeprecated( __METHOD__, '4.3' );
		$props = $this->getPageProps();
		if ( !isset( $props[$name] ) ) {
			return $default;
		}

		return $props[$name];
	}

	/**
	 *
	 * @return \BlueSpice\TargetCacheHandler
	 */
	protected function getCache() {
		return $this->services->getService( 'BSTargetCacheTitle' )->getHandler(
			'pageprops',
			new Target( $this->title )
		);
	}

	/**
	 *
	 * @return array
	 * @deprecated since 4.3 Use MediaWiki core `\PageProps` instead
	 */
	public function getPageProps() {
		wfDeprecated( __METHOD__, '4.3' );
		if ( !$this->title->exists() ) {
			return [];
		}

		$pageProps = $this->getCache()->get();

		// If the cache returns `false`it means the data was not cached or an error occured. Empty
		// arrays are okay
		if ( $pageProps === false ) {
			$pageProps = $this->loadPageProps();
			$this->getCache()->set( $pageProps );
		}

		return $pageProps;
	}

	/**
	 * Fetches the page_props table
	 * @return array
	 */
	private function loadPageProps() {
		$pageProps = [];

		$dbr = $this->services->getDBLoadBalancer()->getConnection( DB_REPLICA );
		$res = $dbr->select(
			'page_props',
			[ 'pp_propname', 'pp_value' ],
			[ 'pp_page' => $this->title->getArticleID() ],
			__METHOD__
		);

		foreach ( $res as $row ) {
			$pageProps[$row->pp_propname] = $row->pp_value;
		}
		return $pageProps;
	}

}
