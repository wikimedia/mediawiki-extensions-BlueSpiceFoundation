<?php

/**
 * Description of Databaseclass
 *
 * @author Sebastian Ulbricht <sebastian.ulbricht@gmx.de>
 */

// Last Review: MRG20100813

// TODO MRG20100810: Bitte alle Funktionen mit PHPDoc versehen
class BsPostGreSQL {
	protected $_mHost		= 'localhost';
	protected $_mUser		= '';
	protected $_mPass		= '';
	protected $_mDatabase	= '';
	protected $_mPort		= 3306;
	// TODO MRG20100810: Warum benötigen wir den socket?
	protected $_mSocket		= NULL;
	protected $_mPrefix		= '';
	
	protected $sLastQuery   = '';
	
	protected $oConn		= NULL;
	protected $oResult		= NULL;

	public function __construct($options) {
		if(isset($options['host'])) {	$this->_mHost		= $options['host']; }
		if(isset($options['user'])) {	$this->_mUser		= $options['user']; }
		if(isset($options['pass'])) {	$this->_mPass		= $options['pass']; }
		if(isset($options['dbname'])) {	$this->_mDatabase	= $options['dbname']; }
		if(isset($options['port'])) {	$this->_mPort		= $options['port']; }
		if(isset($options['socket'])) { $this->_mSocket		= $options['socket']; }
		if(isset($options['prefix'])) { $this->_mPrefix		= $options['prefix']; }
		
		$this->oConn = pg_connect("host={$this->_mHost} port={$this->_mPort} dbname={$this->_mDatabase} user={$this->_mUser} password={$this->_mPass}");
	}
	
	public function getFieldNames($aFields) {
		return join(', ', $aFields);
	}
	
	public function begin() {
		return (bool)pg_query($this->oConn, 'START TRANSACTION');
	}

	public function commit() {
		return (bool)pg_query($this->oConn, 'COMMIT');
	}
	
	public function getTableName($name) {
		// TODO MRG20100810: Was, wenn der Prefix schon drin ist?
		return $this->_mPrefix.$name;
	}

	public function prepareSQL($sql) {
		return str_replace('#__', $this->_mPrefix, $sql);
	}

	public function escape($string) {
		return pg_escape_string($this->oConn, $string);
	}

	public function query($sql, $bufferResult = false) {
		$sql = $this->prepareSQL($sql);
		$this->oResult = pg_query($this->oConn, $sql);
		if(!$this->oResult) {
			return false;
		}
		$this->sLastQuery = $sql;
		return new BsPostGreSQLResultSet($this, $bufferResult);
	}
	
	public function store_result() {
		return new BsPostGreSQLResultSet($this, true);
	}

	public function use_result() {
		return new BsPostGreSQLResultSet($this, false);
	}

	public function sr() {
		return $this->oResult;
	}

	public function ur() {
		return $this->oResult;
	}
	
	public function getLastQuery() {
		return $this->sLastQuery;
	}
	
	public function multi_query($sql) {
		return $this->query($sql);
	}
}



class BsPostGreSQLResultSet extends BsResultSet {
	public function numRows() {
		return pg_num_rows($this->_mResult);
	}

	public function currentField() {
		
	}

	public function fieldCount() {
		
	}

	public function lengths() {
		
	}

	public function dataSeek($offset) {
		
	}

	public function fetchArray($resultType = PGSQL_BOTH) {
		return pg_fetch_array($this->_mResult, NULL, $resultType);
	}

	public function fetchAssoc() {
		return pg_fetch_assoc($this->_mResult);
	}

	public function fetchFieldDirect($number) {
		
	}

	public function fetchField() {
		
	}

	public function fetchFields() {
		
	}

	public function fetchObject($class = NULL, $params = array()) {
		if($class === NULL) {
			return pg_fetch_object($this->_mResult);
		}
		return pg_fetch_object($this->_mResult, NULL, $class, $params);
	}

	public function fetchRow() {
		return pg_fetch_row($this->_mResult);
	}

	public function fieldSeek($number) {
		
	}

	public function free() {
		return pg_free_result($this->_mResult);
	}

	// TODO MRG20100813: Fetchall baut mir ein Array, richtig? 
	public function fetchAll($resultType = MYSQLI_NUM) {
		return pg_fetch_all($this->_mResult);
	}
}