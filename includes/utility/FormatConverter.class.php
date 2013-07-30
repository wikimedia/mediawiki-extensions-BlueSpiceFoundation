<?php
// Last review MRG (21.09.10 10:42)
// TODO MRG (21.09.10 10:42): Kommentar fehlt
// TODO MRG (21.09.10 12:36): nachdems um mwTimestamp geht, muss das in den Adapter
// TODO MRG (21.09.10 10:42): Insgesamt brauchen wir diese Formate internationalisierbar.
// d.h., statt Z.16 $3.$2.$1 muss hier ein i18n-string übergeben werden. Das
// Problem besteht schon bei deutsch und englisch...
/**
 * This class provides functions for different format conversions.
 * @package BlueSpice_AdapterMW
 * @subpackage Utility
 */
class BsFormatConverter {

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe, i.e. "13.02.1983"
	 * @param $sMwTimestamp Timestamp MediaWiki timestamp like "19830213031756"
	 */
	public static function mwTimestampToStandardDateString($sMwTimestamp) {
		// TODO MRG (21.09.10 12:34):
		$sOut = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/",
				// TODO MRG (21.09.10 10:44): i18n
				"$3.$2.$1",
				$sMwTimestamp);
		return $sOut;
	}

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe, i.e. "03:17"
	 * @param $sMwTimestamp Timestamp MediaWiki timestamp like "19830213031756"
	 */
	public static function mwTimestampToShortStandardTimeString($sMwTimestamp) {
		$sOut = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/",
				// TODO MRG (21.09.10 10:44): i18n
				"$4:$5",
				$sMwTimestamp);
		return $sOut;
	}

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe, i.e. "03:17:56"
	 * @param $sMwTimestamp Timestamp MediaWiki timestamp like "19830213031756"
	 */
	public static function mwTimestampToStandardTimeString($sMwTimestamp) {
		$sOut = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/",
				// TODO MRG (21.09.10 10:44): i18n
				"$4:$5:$6",
				$sMwTimestamp);
		return $sOut;
	}

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe, i.e. "13.02.1983, 03:17"
	 * @param $sMwTimestamp Timestamp MediaWiki timestamp like "19830213031756"
	 */
	public static function mwTimestampToStandardShortDateTimeString($sMwTimestamp) {
		$sOut = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/",
				// TODO MRG (21.09.10 10:44): i18n
				"$3.$2.$1, $4:$5",
				$sMwTimestamp);
		return $sOut;
	}

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe.
	 * @param string $sMwTimestamp Timestamp MediaWiki timestamp like "19830213031756" (YmdHis)
	 * @return string Depending on the current date only the time or only day and month. I.e. "03:17" if today was "13.02.1983" or "13.02" if today was "21.7.1983"
	 */
	public static function mwTimestampToDynamicDateTimeString($sMwTimestamp) {
		$sDateTimeOut = '';

		$Y = substr($sMwTimestamp, 0, 4);
		$m = substr($sMwTimestamp, 4, 2);
		$d = substr($sMwTimestamp, 6, 2);
		$h = substr($sMwTimestamp, 8, 2);
		$i = substr($sMwTimestamp, 10, 2);

		// TODO MRG (21.09.10 10:45): auch das muss internationalisierbar sein. sorry...
		if (date('Ymd') == $Y . $m . $d) //Timestamps day is current day
			$sDateTimeOut = $h . ':' . $i; //Only time
		else if ( date('Y') == $Y ) //Timestamps year is current year
			$sDateTimeOut = $d . '.' . $m; //Only day and month
		else
			$sDateTimeOut = $d . '.' . $m . '.' . $Y;
		
		return $sDateTimeOut;
	}

	/**
	 * Converts a typical MediaWiki timestamp String to a date like it is used in middle europe.
	 * @param string $sMySqlTimestamp MySQL timestamp
	 * @return string Depending on the current date only the time or only day and month. I.e. "03:17" if today was "13.02.1983" or "13.02" if today was "21.7.1983"
	 */
	public static function mysqlTimestampToShortDateTimeString($sMySqlTimestamp) {
		$sDateTimeOut = '';

		$sDateTimeOut = date( wfMsg( 'bs-shortdatetime' ), strtotime( $sMySqlTimestamp ) );

		return $sDateTimeOut;
	}

	/**
	 * Converts a typical MySQL timestamp String into an age string, e.g. "2 minutes ago".
	 * @param string $sMySqlTimestamp a MySQL timestamp
	 * @return string A internationalized age string.
	 */
	public static function mysqlTimestampToAgeString( $sMySqlTimestamp ) {
		return self::timestampToAgeString( strtotime( $sMySqlTimestamp ) );
	}

	/**
	 * Converts a typical MediaWiki timestamp String into an age string, e.g. "2 minutes ago".
	 * @param string $sMwTimestamp a MediaWiki timestamp
	 * @return string A internationalized age string.
	 */
	public static function mwTimestampToAgeString( $sMwTimestamp, $bNormalizeToUTC = false ) {
		if ( $bNormalizeToUTC ) {
			$sOldTZ = date_default_timezone_get();
			date_default_timezone_set( 'UTC' );
		}
		$sDate = self::timestampToAgeString( strtotime( $sMwTimestamp ) );
		if ( $bNormalizeToUTC ) {
			date_default_timezone_set( $sOldTZ );
		}
		return $sDate;
	}

	/**
	 * Converts a typical UNIX timestamp String into an age string, e.g. "2 minutes ago".
	 * @param type $sTimestamp a UNIX timestamp
	 * @return type A internationalized age string.
	 */
	public static function timestampToAgeString( $sTimestamp ) {
		/* Idea by sandydakam, http://phpcentral.com/206-php-script-for-duration-calculator.html */
		
		//There is also a javascript version of this method in ArticleInfo.js (should better be in BlueSpice framework)
		$sDateTimeOut = '';
		$sYears = '';
		$sMonths = '';
		$sWeeks = '';
		$sDays = '';
		$sHrs = '';
		$sMins = '';
		$sSecs = '';

		$sTsPast =  $sTimestamp;
		$sTsNow = time();
		$iDuration = $sTsNow - $sTsPast;

		$iYears=floor($iDuration/(60*60*24*365));$iDuration%=60*60*24*365;
		$iMonths=floor($iDuration/(60*60*24*30.5));$iDuration%=60*60*24*30.5;
		$iWeeks=floor($iDuration/(60*60*24*7));$iDuration%=60*60*24*7;
		$iDays=floor($iDuration/(60*60*24));$iDuration%=60*60*24;
		$iHrs=floor($iDuration/(60*60));$iDuration%=60*60;
		$iMins=floor($iDuration/60);$iSecs=$iDuration%60;


		if ($iYears == 1) { $sYears = wfMsg( 'bs-year-duration', $iYears ); }
		if ($iYears > 1) { $sYears = wfMsg( 'bs-years-duration', $iYears ); }
		
		if ($iMonths == 1) { $sMonths = wfMsg( 'bs-month-duration', $iMonths ); }
		if ($iMonths > 1) { $sMonths = wfMsg( 'bs-months-duration', $iMonths ); }

		if ($iWeeks == 1) { $sWeeks = wfMsg( 'bs-week-duration', $iWeeks ); }
		if ($iWeeks > 1) { $sWeeks = wfMsg( 'bs-weeks-duration', $iWeeks ); }

		if ($iDays == 1) { $sDays = wfMsg( 'bs-day-duration', $iDays ); }
		if ($iDays > 1) { $sDays = wfMsg( 'bs-days-duration', $iDays ); }

		if ($iHrs == 1) { $sHrs = wfMsg( 'bs-hour-duration', $iHrs ); }
		if ($iHrs > 1) { $sHrs = wfMsg( 'bs-hours-duration', $iHrs ); }

		if ($iMins == 1) { $sMins = wfMsg( 'bs-min-duration', $iMins ); }
		if ($iMins > 1) { $sMins = wfMsg( 'bs-mins-duration', $iMins ); }

		if ($iSecs == 1) { $sSecs = wfMsg( 'bs-sec-duration', $iSecs ); }
		if ($iSecs > 1) { $sSecs = wfMsg( 'bs-secs-duration', $iSecs ); }

		if ($iYears > 0) $sDateTimeOut = $sMonths ? wfMsg( 'bs-two-units-ago', $sYears, $sMonths ) : wfMsg( 'bs-one-unit-ago', $sYears );
		else if ($iMonths > 0) $sDateTimeOut = $sWeeks ? wfMsg( 'bs-two-units-ago', $sMonths, $sWeeks ) : wfMsg( 'bs-one-unit-ago', $sMonths );
		else if ($iWeeks > 0) $sDateTimeOut = $sDays ? wfMsg( 'bs-two-units-ago', $sWeeks, $sDays ) : wfMsg( 'bs-one-unit-ago', $sWeeks );
		else if ($iDays > 0) $sDateTimeOut = $sHrs ? wfMsg( 'bs-two-units-ago', $sDays, $sHrs ) : wfMsg( 'bs-one-unit-ago', $sDays );
		else if ($iHrs > 0) $sDateTimeOut = $sMins ? wfMsg( 'bs-two-units-ago', $sHrs, $sMins ) : wfMsg( 'bs-one-unit-ago', $sHrs );
		else if ($iMins > 0) $sDateTimeOut = $sSecs ? wfMsg( 'bs-two-units-ago', $sMins, $sSecs ) : wfMsg( 'bs-one-unit-ago', $sMins );
		else if ($iSecs > 0) $sDateTimeOut = wfMsg( 'bs-one-unit-ago', $sSecs );
		else if ($iSecs == 0) $sDateTimeOut = wfMsg( 'bs-now' );

		return $sDateTimeOut;
	}
}