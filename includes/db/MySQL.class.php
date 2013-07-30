<?php

/**
 * Description of BsMySQL
 *
 * @author Sebastian Ulbricht <sebastian.ulbricht@gmx.de>
 */

// Last Review: MRG20100813

// TODO MRG20100810: Bitte alle Funktionen mit PHPDoc versehen
class BsMySQL extends mysqli {
	protected $_mHost		= 'localhost';
	protected $_mUser		= '';
	protected $_mPass		= '';
	protected $_mDatabase	= '';
	protected $_mPort		= 3306;
	// TODO MRG20100810: Warum benötigen wir den socket?
	protected $_mSocket		= NULL;
	protected $_mPrefix		= '';
	
	protected $sLastQuery   = '';

	public function __construct($options) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if(isset($options['host'])) {	$this->_mHost		= $options['host']; }
		if(isset($options['user'])) {	$this->_mUser		= $options['user']; }
		if(isset($options['pass'])) {	$this->_mPass		= $options['pass']; }
		if(isset($options['dbname'])) {	$this->_mDatabase	= $options['dbname']; }
		if(isset($options['port'])) {	$this->_mPort		= $options['port']; }
		if(isset($options['socket'])) { $this->_mSocket		= $options['socket']; }
		if(isset($options['prefix'])) { $this->_mPrefix		= $options['prefix']; }
		
		parent::__construct($this->_mHost, $this->_mUser, $this->_mPass, $this->_mDatabase, $this->_mPort, $this->_mSocket);
		wfProfileOut( 'BS::'.__METHOD__ );
	}
	
	public function getFieldNames($aFields) {
		return '`'.join('`, `', $aFields).'`';
	}

	public function begin() {
		$this->autocommit(false);
	}

	public function commit() {
		parent::commit();
		$this->autocommit(true);
	}
	
	public function getTableName($name) {
		// TODO MRG20100810: Was, wenn der Prefix schon drin ist?
		return $this->_mPrefix.$name;
	}

	public function prepareSQL($sql) {
		return str_replace('#__', $this->_mPrefix, $sql);
	}

	public function escape($string) {
		return $this->real_escape_string($string);
	}

	public function query($sql, $bufferResult = false) {
		$sql = $this->prepareSQL($sql);
		$this->real_query($sql);
		$this->sLastQuery = $sql;
		return new BsMySQLResultSet($this, $bufferResult);
	}
	// TODO MRG20100810: Das versteh ich nicht. Also alles wird gespeichert in query. Dann gibt es
	// store und use, die aber immer das Ergebnisset zurückgeben.
	public function store_result() {
		return new BsMySQLResultSet($this, true);
	}

	public function use_result() {
		return new BsMySQLResultSet($this, false);
	}

	public function sr() {
		return parent::store_result();
	}

	public function ur() {
		return parent::use_result();
	}
	
	public function getLastQuery() {
		return $this->sLastQuery;
	}
}

// TODO MRG20100813: Wieso brauchen wir hier ein eigenes ResultSet? Es geht wohl um den Buffer, oder?
class BsMySQLResultSet extends BsResultSet {
	public function numRows() {
		if(!$this->_mResult) { return 0; }
		return $this->_mResult->num_rows;
	}

	public function currentField() {
		if(!$this->_mResult) { return NULL; }
		return $this->_mResult->current_field;
	}

	public function fieldCount() {
		if(!$this->_mResult) { return 0; }
		return $this->_mResult->field_count;
	}

	public function lengths() {
		if(!$this->_mResult) { return 0; }
		return $this->_mResult->lengths;
	}

	public function dataSeek($offset) {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->data_seek($offset);
	}

	public function fetchArray($resultType = MYSQLI_BOTH) {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_array($resultType);
	}

	public function fetchAssoc() {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_assoc();
	}

	public function fetchFieldDirect($number) {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_field_direct($number);
	}

	public function fetchField() {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_field();
	}

	public function fetchFields() {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_fields();
	}

	public function fetchObject($class = NULL, $params = array()) {
		if(!$this->_mResult) { return false; }
		if($class === NULL) {
			return $this->_mResult->fetch_object();
		}
		return $this->_mResult->fetch_object($class, $params);
	}

	public function fetchRow() {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->fetch_row();
	}

	public function fieldSeek($number) {
		if(!$this->_mResult) { return false; }
		return $this->_mResult->field_seek($number);
	}

	public function free() {
		if(!$this->_mResult) { return false; }
		$this->_mResult->free();
		$this->_mResult = NULL;
	}

	// TODO MRG20100813: Fetchall baut mir ein Array, richtig? 
	public function fetchAll($resultType = MYSQLI_NUM) {
		if(!$this->_mResult) { return false; }
		if(method_exists($this->_mResult, 'fetch_all')) {
			return $this->_mResult->fetch_all($resultType);
		}
		if(!$this->_mResult->num_rows) {
			return NULL;
		}
		$tmp = array();
		while($row = $this->_mResult->fetch_array($resultType)) {
			$tmp[] = $row;
		}
		return $tmp;
	}
}
