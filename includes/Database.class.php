<?php

/**
 * Description of BsDatabase
 *
 * @author Sebastian Ulbricht <sebastian.ulbricht@gmx.de>
 */
// Last Review: MRG20100813
// TODO MRG20100810: Bitte alle Funktionen mit PHPDoc versehen
class BsDatabase {

	protected static $_prInstances = array();

	/**
	 * N-gleton for BlueSpice DBAL
	 * @param String $sKey
	 * @param Array $aOptions
	 * @return BsDatabase
	 */
	public static function getInstance($sKey, $options = NULL) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$sKey = strtoupper($sKey);
		if (!isset(self::$_prInstances[$sKey])) {
			global $wgDBtype, $wgDBserver, $wgDBname, $wgDBuser, $wgDBpassword, $wgDBprefix;
			$type = '';
			switch($wgDBtype) {
				case 'mysql':
					$type = 'MySQL';
					break;
				case 'postgres':
					$type = 'PostGreSQL';
					break;
				case 'oracle':
					$type = 'OracleDB';
					break;
				default:
					$type = 'MySQL';
					break;
			}
			$options = array(
				'host' => $wgDBserver,
				'user' => $wgDBuser,
				'pass' => $wgDBpassword,
				'dbname' => $wgDBname,
				'prefix' => $wgDBprefix,
				'type' => $type
			);
			if (isset($options['type'])) {
				$type = $options['type'];
				if($options['type'] == 'OracleDB') {
					$options['host'] = 'localhost';
					$options['dbname'] = $wgDBserver;
				}
			}
			else {
				$type = 'MySQL';
			}
			require_once(BSROOTDIR.DS.'includes'.DS.'db'.DS.$type.'.class.php');
			$class = 'Bs'.$type;
			self::$_prInstances[$sKey] = new $class($options);
		}
		wfProfileOut( 'BS::'.__METHOD__ );
		return self::$_prInstances[$sKey];
	}
}

// TODO MRG20100813: Wieso brauchen wir hier ein eigenes ResultSet? Es geht wohl um den Buffer, oder?
// TODO LILU (03.05.11 11:52): Das brauchen wir um mehrere Datenbanken zu unterstützen
// und auf alle mit dem gleichen Interface zugreifen zu können.
class BsResultSet {

	protected $_mResult = NULL;

	public function __construct($obj, $bufferResult) {
		if ($bufferResult) {
			$this->_mResult = $obj->sr();
		} else {
			$this->_mResult = $obj->ur();
		}
	}

}
