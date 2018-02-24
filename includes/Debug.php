<?php

class BSDebug {

	public static $logEnabled = true;

	/**
	 * Writes a line to a log file.
	 * Default log location: php error_log
	 * @param string $sLine
	 */
	public static function log( $sLine ) {
		self::writeLog( $sLine );
	}

	/**
	 * Writes current call stack to a log file
	 * Default log location: php error_log
	 * @param array $aParams currently not in use
	 */
	public static function logSimpleCallStack( $aParams = array() ){
		$aBackTrace = debug_backtrace(); //TODO: Use wfDebugBacktrace()?
		$iLength = count( $aBackTrace );
		$aStack = array();
		for( $i = 2; $i < $iLength; $i++ ) {
			$aStack[] = self::formatLine( $aBackTrace[$i] );
		}

		$sStack = implode( "\n", $aStack );

		self::writeLog($sStack);
	}

	/**
	 * Writes the caller method to a log file
	 * Default log location: php error_log
	 * @param array $aParams currently not used
	 */
	public static function logCaller( $aParams = array() ){
		$aBackTrace = debug_backtrace(); //TODO: Use wfDebugBacktrace()?
		//Index of "2", because "0" is this method call and "1" is the caller
		//of this method. But we want the caller of the caller of this method.
		$sLine = self::formatLine( $aBackTrace[2] );

		self::writeLog( $sLine );
	}

	/**
	 * Prepares lines of call stacks so they can be written to a log
	 * @param array $aBackTraceLine
	 * @param array $aParams currently not used
	 * @return string
	 */
	protected static function formatLine( $aBackTraceLine, $aParams = array() ) {
		$sLine = '';
		if( isset( $aBackTraceLine['class'] ) ) {
			$sLine = $aBackTraceLine['class'].'::';
		}
		$sLine .= $aBackTraceLine['function'];

		return $sLine;
	}

	/**
	 * Writes the content of a variable to a log file
	 * Default log location: php error_log
	 * @param mixed $mVar The variable to log
	 * @param array $aParams possible parameters are
	 *   "format" => "json"
	 *   "mark" => true
	 */
	public static function logVar( $mVar, $aParams = array() ) {
		if( empty( $aParams['mark'] ) ) {
			$aBackTrace = debug_backtrace();
			$sLine = self::formatLine( $aBackTrace[1] );
			self::writeLog( $sLine );
		}

		if( isset( $aParams['format'] ) && strtolower( $aParams['format'] ) == 'json') {
			$sOut = FormatJson::encode( $mVar, true );
		}
		else {
			$sOut = var_export( $mVar, true );
		}

		self::writeLog( $sOut );
	}

	/**
	 * Writes to log if condition is ok
	 * Default log location: php error_log
	 * @param bool $bCondition The condition when to log
	 * @param mixed $mVar The variable to log
	 * @param array $aParams see logVar
	 */
	public static function logVarConditionally( $bCondition, $mVar, $aParams = array() ) {
		if( $bCondition ) {
			self::logVar( $mVar, $aParams );
		}
	}

	/**
	 * Actually performs the write to log
	 * @param string $out
	 */
	protected static function writeLog( $out ) {
		if ( self::$logEnabled ) {
			error_log( $out );
		}
	}
}
