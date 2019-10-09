<?php

/**
 * Writes a line to a log file.
 * Default log location: php error_log
 * @param string $line
 */
function bsDebugLog( $line ) {
	\BlueSpice\Debug::log( $line );
}

/**
 * Writes current call stack to a log file
 * Default log location: php error_log
 * @param array $params currently not in use
 */
function bsDebugLogSimpleCallStack( array $params = [] ) {
	\BlueSpice\Debug::logSimpleCallStack( $params );
}

/**
 * Writes the caller method to a log file
 * Default log location: php error_log
 * @param array $params currently not used
 */
function bsDebugLogCaller( array $params = [] ) {
	\BlueSpice\Debug::logCaller( $params );
}

/**
 * Writes the content of a variable to a log file
 * Default log location: php error_log
 * @param mixed $var The variable to log
 * @param array $params possible parameters are
 *   "format" => "json"
 *   "mark" => true
 */
function bsDebugLogVar( $var, array $params = [] ) {
	\BlueSpice\Debug::logVar( $var, $params );
}

/**
 * Writes to log if condition is ok
 * Default log location: php error_log
 * @param bool $condition The condition when to log
 * @param mixed $var The variable to log
 * @param array $params see logVar
 */
function bsDebugLogVarConditionally( $condition, $var, array $params = [] ) {
	\BlueSpice\Debug::logVarConditionally( $condition, $var, $params );
}
