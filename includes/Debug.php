<?php

class BSDebug {
	public static function logSimpleCallStack( $aParams = array() ){
		$aBackTrace = debug_backtrace(); //TODO: Use wfDebugBacktrace()?
		$iLength = count( $aBackTrace );
		$aStack = array();
		for( $i = 2; $i < $iLength; $i++ ) {
			$aStack[] = self::formatLine($aBackTrace[$i]);
		}

		$sStack = implode( "\n", $aStack );

		error_log($sStack);
	}

	public static function logCaller( $aParams = array() ){
		$aBackTrace = debug_backtrace(); //TODO: Use wfDebugBacktrace()?
		//Index of "2", because "0" is this method call and "1" is the caller
		//of this method. But we want the caller of the caller of this method.
		$sLine = self::formatLine( $aBackTrace[2] );

		error_log($sLine);
	}

	protected static function formatLine( $aBackTraceLine, $aParams = array() ) {
		$sLine = '';
		if( isset($aBackTraceLine['class'])) $sLine = $aBackTraceLine['class'].'::';
		$sLine .= $aBackTraceLine['function'];

		return $sLine;
	}

	public static function logVar( $mVar, $aParams = array() ) {
		if( empty( $aParams['mark'] ) ) {
			$aBackTrace = debug_backtrace();
			$sLine = self::formatLine( $aBackTrace[1] );
			error_log($sLine);
		}

		if( isset( $aParams['format'] ) && strtolower( $aParams['format'] ) == 'json') {
			$sOut = FormatJson::encode( $mVar, true );
		}
		else {
			$sOut = var_export($mVar, true);
		}

		error_log( $sOut );
	}

	public static function logVarConditionally($bCondition, $mVar, $aParams = array()) {
		if( $bCondition ) {
			self::logVar($mVar, $aParams);
		}
	}
}