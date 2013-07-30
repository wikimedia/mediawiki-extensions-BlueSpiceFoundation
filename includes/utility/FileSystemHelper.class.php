<?php

class BsFileSystemHelper {
	
	/**
	 *
	 * @param string $sSubDirName
	 * @return Status 
	 */
	public static function ensureCacheDirectory( $sSubDirName = '' ) {
		wfProfileIn( __METHOD__ );
		wfProfileOut( __METHOD__ );
		return Status::newGood();
	}
	
	/**
	 *
	 * @param string $sSubDirName
	 * @return Status 
	 */
	public static function ensureDataDirectory( $sSubDirName = '') {
		wfProfileIn( __METHOD__ );
		wfProfileOut( __METHOD__ );
		return Status::newGood();
	}
	
	/**
	 *
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status 
	 */
	public static function saveToCacheDirectory( $sSubDirName, $data ) {
		wfProfileIn( __METHOD__ );
		self::ensureCacheDirectory($sSubDirName);
		wfProfileOut( __METHOD__ );
		return Status::newGood();
	}
	
	/**
	 *
	 * @param string $sSubDirName
	 * @param mixed $data
	 * @return Status 
	 */
	public static function saveToDataDirectory( $sSubDirName, $data ) {
		wfProfileIn( __METHOD__ );
		self::ensureDataDirectory($sSubDirName);
		wfProfileOut( __METHOD__ );
		return Status::newGood();
	}
	
	/**
	 * HINT: http://fr2.php.net/manual/en/function.copy.php#91010
	 * @param string $sSource
	 * @param type $sDestination
	 */
	public static function copyRecursive($sSource,$sDestination) {
		$rDir = opendir($sSource);
		wfMkdirParents($sDestination);
		while(false !== ( $sFileName = readdir($rDir)) ) {
			if (( $sFileName != '.' ) && ( $sFileName != '..' )) {
				if ( is_dir($sSource . '/' . $sFileName) ) {
					self::copyRecursive($sSource . '/' . $sFileName,$sDestination . '/' . $sFileName);
				}
				else {
					copy($sSource . '/' . $sFileName,$sDestination . '/' . $sFileName);
				}
			}
		}
		closedir($rDir);
	}
}