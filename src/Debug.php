<?php

namespace BlueSpice;

class Debug {

	public static $logEnabled = true;

	/**
	 * Writes a line to a log file.
	 * Default log location: php error_log
	 * @param string $line
	 */
	public static function log( $line ) {
		static::writeLog( $line );
	}

	/**
	 * Writes current call stack to a log file
	 * Default log location: php error_log
	 * @param array $params currently not in use
	 */
	public static function logSimpleCallStack( $params = [] ) {
		// TODO: Use wfDebugBacktrace()?
		$backTrace = debug_backtrace();
		$length = count( $backTrace );
		$stack = [];
		for ( $i = 2; $i < $length; $i++ ) {
			$stack[] = static::formatLine( $backTrace[$i] );
		}

		$sStack = implode( "\n", $stack );

		static::writeLog( $sStack );
	}

	/**
	 * Writes the caller method to a log file
	 * Default log location: php error_log
	 * @param array $params currently not used
	 */
	public static function logCaller( $params = [] ) {
		// TODO: Use wfDebugBacktrace()?
		$backTrace = debug_backtrace();
		// Index of "2", because "0" is this method call and "1" is the caller
		// of this method. But we want the caller of the caller of this method.
		$line = static::formatLine( $backTrace[2] );

		static::writeLog( $line );
	}

	/**
	 * Prepares lines of call stacks so they can be written to a log
	 * @param array $backTraceLine
	 * @param array $params currently not used
	 * @return string
	 */
	protected static function formatLine( $backTraceLine, $params = [] ) {
		$line = '';
		if ( isset( $backTraceLine['class'] ) ) {
			$line = $backTraceLine['class'] . '::';
		}
		$line .= $backTraceLine['function'];

		return $line;
	}

	/**
	 * Writes the content of a variable to a log file
	 * Default log location: php error_log
	 * @param mixed $var The variable to log
	 * @param array $params possible parameters are
	 *   "format" => "json"
	 *   "mark" => true
	 */
	public static function logVar( $var, $params = [] ) {
		if ( empty( $params['mark'] ) ) {
			$backTrace = debug_backtrace();
			$line = static::formatLine( $backTrace[1] );
			static::writeLog( $line );
		}

		if ( isset( $params['format'] ) && strtolower( $params['format'] ) == 'json' ) {
			$out = FormatJson::encode( $var, true );
		} else {
			$out = var_export( $var, true );
		}

		static::writeLog( $out );
	}

	/**
	 * Writes to log if condition is ok
	 * Default log location: php error_log
	 * @param bool $condition The condition when to log
	 * @param mixed $var The variable to log
	 * @param array $params see logVar
	 */
	public static function logVarConditionally( $condition, $var, $params = [] ) {
		if ( $condition ) {
			static::logVar( $var, $params );
		}
	}

	/**
	 * Actually performs the write to log
	 * @param string $out
	 */
	protected static function writeLog( $out ) {
		if ( static::$logEnabled ) {
			error_log( $out );
		}
	}
}
