<?php
/**
 * Encapsulation for standard Ext.data.Store / Ext.data.Proxy parameters
 */
class BsExtJSStoreParams {
	
	const DIR_ASC = 'ASC';
	const DIR_DESC = 'DESC';

	protected $oRequest = null;

	protected $iLimit; //25
	protected $iPage; //1
	protected $iStart; //0
	protected $aSort; //Array 
	protected $sCallback; //For JSONP
	protected $sQuery; //For filtering
	protected $sDirection;
	protected $aFilter;

	/**
	 * Factory method for BsExtJSStoreParams.
	 * @return null|BsExtJSStoreParams 
	 */
	public static function newFromRequest(){
		$oRequest = RequestContext::getMain()->getRequest();

		//Mandatory (?)
		$iLimit  = $oRequest->getInt( 'limit', null );
		$iPage   = $oRequest->getInt( 'page', null );
		$iStart  = $oRequest->getInt( 'start', null );

		//Optional
		$aSort = FormatJson::decode( $oRequest->getVal( 'sort' ) );
		if ( is_array( $aSort ) ) {
			//TODO: Multisort!
			$aSort = $aSort[0];
		} else {
			$sSort      = $oRequest->getVal( 'sort', '' );
			$sDirection = $oRequest->getVal( 'dir', 'ASC' );
			$aSort = new stdClass();
			$aSort->property = $sSort;
			$aSort->direction = $sDirection;
		}

		$sCallback = $oRequest->getVal( 'callback' );
		$sQuery    = $oRequest->getVal( 'query', '' );

		$aFilter = FormatJson::decode( $oRequest->getVal('filter', '[]') );

		//TODO: Really return null or better return object with default values?
		if( $iLimit === null || $iPage === null || $iStart === null ) {
			return null;
		}

		return new self( array(
			'limit'     => $iLimit,
			'page'      => $iPage,
			'start'     => $iStart,
			'sort'      => $aSort->property,
			'direction' => strtoupper( $aSort->direction ),
			'callback'  => $sCallback,
			'query'     => $sQuery,
			'request'   => $oRequest,
			'filter'    => $aFilter,
		) );
	}

	private function __construct( $aConf ) {
		$this->iLimit     = $aConf['limit'];
		$this->iPage      = $aConf['page'];
		$this->iStart     = $aConf['start'];
		$this->sSort      = $aConf['sort'];
		$this->sDirection = $aConf['direction'];
		$this->sCallback  = $aConf['callback'];
		$this->sQuery     = $aConf['query'];
		$this->oRequest   = $aConf['request'];
		$this->aFilter    = $aConf['filter'];
	}

	/**
	 * Getter for "limit" param
	 * @return int The "limit" parameter for the ExtJS Store backend
	 */
	public function getLimit() {
		return $this->iLimit;
	}

	/**
	 * Getter for "page" param
	 * @return int The "page" parameter for the ExtJS Store backend
	 */
	public function getPage() {
		return $this->iPage;
	}

	/**
	 * Getter for "start" param
	 * @return int The "start" parameter for the ExtJS Store backend
	 */
	public function getStart() {
		//TODO: mabye this can be calculated from "page" and "limit"; Examine behavior of Ext.data.Store / Ext.data.Proxy
		return $this->iStart;
	}

	/**
	 * Getter for "sort" param
	 * @return int The "sort" parameter for the ExtJS Store backend
	 */
	public function getSort( $sDefault = '' ) {
		if ( empty( $this->sSort ) ) return $sDefault;
		return $this->sSort;
	}

	/**
	 * Getter for "dir" param
	 * @return int The "dir" parameter for the ExtJS Store backend
	 */
	public function getDirection() {
		return $this->sDirection;
	}

	/**
	 * Getter for "query" param
	 * @return int The "query" parameter for the ExtJS Store backend
	 */
	public function getQuery() {
		return $this->sQuery;
	}
	
	public function getFilter() {
		return $this->aFilter;
	}

	/**
	 * Surrounds data with JSONP callback
	 * @param mixed $mData
	 * @return string
	 */
	public function maybeApplyCallback( $mData ) {
		$result = FormatJson::encode( $mData );
		if( !empty($this->sCallback) ) {
			$result = sprintf(
				$this->sCallback.'( %s );',
				$result
			);
		}
		return $result;
	}

	/**
	 * 
	 * @return WebRequest
	 */
	public function getRequest() {
		return $this->oRequest;
	}
}
