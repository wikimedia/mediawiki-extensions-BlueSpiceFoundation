<?php

/**
 * Description of Databaseclass
 *
 * @author Sebastian Ulbricht <sebastian.ulbricht@gmx.de>
 */
class BsOracleDB {

	protected $_mHost = '';
	protected $_mUser = '';
	protected $_mPass = '';
	protected $_mDatabase = '';
	protected $_mPort = 3306;
	// TODO MRG20100810: Warum benötigen wir den socket?
	protected $_mSocket = NULL;
	protected $_mPrefix = '';
	protected $sLastQuery = '';
	protected $iAutoCommit = OCI_COMMIT_ON_SUCCESS;
	protected $oConn = NULL;
	protected $oResult = NULL;

	public function __construct($options) {
		if (isset($options['host'])) {
			$this->_mHost = $options['host'];
		}
		if (isset($options['user'])) {
			$this->_mUser = $options['user'];
		}
		if (isset($options['pass'])) {
			$this->_mPass = $options['pass'];
		}
		if (isset($options['dbname'])) {
			$this->_mDatabase = $options['dbname'];
		}
		if (isset($options['port'])) {
			$this->_mPort = $options['port'];
		}
		if (isset($options['socket'])) {
			$this->_mSocket = $options['socket'];
		}
		if (isset($options['prefix'])) {
			$this->_mPrefix = $options['prefix'];
		}

		$sConnectionString = '';
		$sConnectionString .= $this->_mHost ? $this->_mHost . '/' : '';
		$sConnectionString .= $this->_mDatabase;
		$sConnectionString .= $this->_mPort ? ':' . $this->_mPort : '';

		$this->oConn = oci_connect($this->_mUser, $this->_mPass, $sConnectionString);
		if (!$this->oConn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
	}

	public function getFieldNames($aFields) {
		return join(', ', $aFields);
	}

	public function begin() {
		$this->iAutoCommit = OCI_NO_AUTO_COMMIT;
		return true;
	}

	public function commit() {
		$r = oci_commit($this->oConn);
		if (!$r ) {
			$e = oci_error($this->oConn);
			trigger_error(htmlentities($e['message']), E_USER_ERROR);
		}
		$this->iAutoCommit = OCI_COMMIT_ON_SUCCESS;
		return $r;
	}

	public function getTableName($name) {
		// TODO MRG20100810: Was, wenn der Prefix schon drin ist?
		return $this->_mPrefix . $name;
	}

	public function prepareSQL($sql) {
		return str_replace('#__', $this->_mPrefix, $sql);
	}

	public function escape($string) {
		return $string;
	}

	public function query($sql, $bufferResult = false) {
		$sql = $this->prepareSQL($sql);
		$this->oResult = oci_parse($this->oConn, $sql);
		if (!oci_execute($this->oResult, $this->iAutoCommit)) {
			$e = oci_error($this->oConn);
			trigger_error(htmlentities($e['message'] . '<br /><br />query was; "' . $sql . '"'), E_USER_ERROR);
			return false;
		}
		$this->sLastQuery = $sql;
		return new BsOracleDBResultSet($this, $bufferResult);
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
		$this->begin();
		foreach ($sql as $query) {
			$this->query($query);
		}
		return $this->commit();
	}

}

class BsOracleDBResultSet extends BsResultSet {

	public function numRows() {
		return oci_num_rows($this->_mResult);
	}

	public function currentField() {
		
	}

	public function fieldCount() {
		
	}

	public function lengths() {
		
	}

	public function dataSeek($offset) {
		
	}

	public function fetchArray($resultType = OCI_BOTH) {
		return oci_fetch_array($this->_mResult, $resultType);
	}

	public function fetchAssoc() {
		return oci_fetch_assoc($this->_mResult);
	}

	public function fetchFieldDirect($number) {
		
	}

	public function fetchField() {
		
	}

	public function fetchFields() {
		
	}

	public function fetchObject($class = NULL, $params = array()) {
		return oci_fetch_object($this->_mResult);
	}

	public function fetchRow() {
		return oci_fetch_row($this->_mResult);
	}

	public function fieldSeek($number) {
		
	}

	public function free() {
		return oci_free_statement($this->_mResult);
	}

	public function fetchAll($resultType = OCI_NUM) {
		$tmp = array();
		oci_fetch_all($this->_mResult, $tmp, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + $resultType);
		return $tmp;
	}

}