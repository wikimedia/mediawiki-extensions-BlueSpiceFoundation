<?php
/**
 * Encapsulation for standard Ext.data.Store / Ext.data.Proxy parameters
 */
class BsExtJSStoreParams {
	
	protected $iLimit; //25
	protected $iPage; //1
	protected $iStart; //0
	protected $sSort; //fieldname
	protected $sDir; //'ASC' or 'DESC'
	protected $sCallback; //For JSONP
	
	/**
	 * Factory method for BsExtJSStoreParams.
	 * @return null|BsExtJSStoreParams 
	 */
	public static function newFromRequest(){
		$oRequest = RequestContext::getMain()->getRequest();
		
		//Mandatory (?)
		$iLimit = $oRequest->getInt( 'limit', null );
		$iPage  = $oRequest->getInt( 'page', null );
		$iStart = $oRequest->getInt( 'start', null );
		
		//Optional
		$sSort     = $oRequest->getVal( 'sort' );
		$sDir      = $oRequest->getVal( 'dir' );
		$sCallback = $oRequest->getVal( 'callback' );
		
		//TODO: Really return null or better return object with default values?
		if( $iLimit === null || $iPage === null || $iStart === null ) {
			return null;
		}
		
		return new self( $iLimit, $iPage, $iStart );
	}
	
	private function __construct( $iLimit, $iPage, $iStart, $iSort = '', $sDir = '', $sCallback = '' ) {
		$this->iLimit = $iLimit;
		$this->iPage = $iPage;
		$this->iStart = $iStart;
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
}