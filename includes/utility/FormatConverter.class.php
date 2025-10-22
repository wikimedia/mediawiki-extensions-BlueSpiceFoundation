<?php
// Last review MRG (21.09.10 10:42)
// TODO MRG (21.09.10 10:42): Kommentar fehlt
/**
 * This class provides functions for different format conversions.
 * @package BlueSpice_Core
 * @subpackage Utility
 */
class BsFormatConverter {

	/**
	 * DEPRECATED
	 * Converts a typical MediaWiki timestamp String into an age string, e.g. "2 minutes ago".
	 * @deprecated since version 3.1 - Use \BlueSpice\Timestamp->getAgeString instead
	 * @param string $sMwTimestamp a MediaWiki timestamp
	 * @return string A internationalized age string.
	 */
	public static function mwTimestampToAgeString( $sMwTimestamp, $bNormalizeToUTC = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		return ( new \BlueSpice\Timestamp( $sMwTimestamp ) )->getAgeString();
	}

	/**
	 * DEPRECATED
	 * Converts a typical UNIX timestamp String into an age string, e.g. "2 minutes ago".
	 * @deprecated since version 3.1 - Use \BlueSpice\Timestamp->getAgeString instead
	 * @param type $sTimestamp a UNIX timestamp
	 * @return type A internationalized age string.
	 */
	public static function timestampToAgeString( $sTimestamp ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		/* Idea by sandydakam, http://phpcentral.com/206-php-script-for-duration-calculator.html */

		// There is also a javascript version of this method in ArticleInfo.js (should better be
		// in BlueSpice framework)
		$sDateTimeOut = '';
		$sYears = '';
		$sMonths = '';
		$sWeeks = '';
		$sDays = '';
		$sHrs = '';
		$sMins = '';
		$sSecs = '';

		$sTsPast = $sTimestamp;
		$sTsNow = time();
		$iDuration = $sTsNow - $sTsPast;

		$iYears = floor( $iDuration / ( 60 * 60 * 24 * 365 ) );
		$iDuration %= 60 * 60 * 24 * 365;

		$iMonths = floor( $iDuration / ( 60 * 60 * 24 * 30.5 ) );
		$iDuration %= 60 * 60 * 24 * 30.5;

		$iWeeks = floor( $iDuration / ( 60 * 60 * 24 * 7 ) );
		$iDuration %= 60 * 60 * 24 * 7;

		$iDays = floor( $iDuration / ( 60 * 60 * 24 ) );
		$iDuration %= 60 * 60 * 24;

		$iHrs = floor( $iDuration / ( 60 * 60 ) );
		$iDuration %= 60 * 60;

		$iMins = floor( $iDuration / 60 );
		$iSecs = $iDuration % 60;

		if ( $iYears > 0 ) {
			$sYears = wfMessage( 'bs-years-duration', $iYears )->text();
		}
		if ( $iMonths > 0 ) {
			$sMonths = wfMessage( 'bs-months-duration', $iMonths )->text();
		}
		if ( $iWeeks > 0 ) {
			$sWeeks = wfMessage( 'bs-weeks-duration', $iWeeks )->text();
		}
		if ( $iDays > 0 ) {
			$sDays = wfMessage( 'bs-days-duration', $iDays )->text();
		}
		if ( $iHrs > 0 ) {
			$sHrs = wfMessage( 'bs-hours-duration', $iHrs )->text();
		}
		if ( $iMins > 0 ) {
			$sMins = wfMessage( 'bs-mins-duration', $iMins )->text();
		}
		if ( $iSecs > 0 ) {
			$sSecs = wfMessage( 'bs-secs-duration', $iSecs )->text();
		}

		if ( $iYears > 0 ) {
			$sDateTimeOut = $sMonths
				? wfMessage( 'bs-two-units-ago', $sYears, $sMonths )->text()
				: wfMessage( 'bs-one-unit-ago', $sYears )->text();
		} elseif ( $iMonths > 0 ) {
			$sDateTimeOut = $sWeeks
				? wfMessage( 'bs-two-units-ago', $sMonths, $sWeeks )->text()
				: wfMessage( 'bs-one-unit-ago', $sMonths )->text();
		} elseif ( $iWeeks > 0 ) {
			$sDateTimeOut = $sDays
				? wfMessage( 'bs-two-units-ago', $sWeeks, $sDays )->text()
				: wfMessage( 'bs-one-unit-ago', $sWeeks )->text();
		} elseif ( $iDays > 0 ) {
			$sDateTimeOut = $sHrs
				? wfMessage( 'bs-two-units-ago', $sDays, $sHrs )->text()
				: wfMessage( 'bs-one-unit-ago', $sDays )->text();
		} elseif ( $iHrs > 0 ) {
			$sDateTimeOut = $sMins
				? wfMessage( 'bs-two-units-ago', $sHrs, $sMins )->text()
				: wfMessage( 'bs-one-unit-ago', $sHrs )->text();
		} elseif ( $iMins > 0 ) {
			$sDateTimeOut = $sSecs
				? wfMessage( 'bs-two-units-ago', $sMins, $sSecs )->text()
				: wfMessage( 'bs-one-unit-ago', $sMins )->text();
		} elseif ( $iSecs > 0 ) {
			$sDateTimeOut = wfMessage( 'bs-one-unit-ago', $sSecs )->text();
		} elseif ( $iSecs == 0 ) {
			$sDateTimeOut = wfMessage( 'bs-now' )->text();
		}

		return $sDateTimeOut;
	}
}
