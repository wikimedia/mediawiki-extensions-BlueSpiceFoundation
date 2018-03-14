<?php
/**
 * This class contains helpful methods for processing strings.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Robert Vogel
 *
 * $LastChangedDate: 2013-06-12 15:58:22 +0200 (Mi, 12 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9700 $

 */
class BsStringHelper {

	/**
	 * Reduces the length of a string in a smart way.
	 * @param String  $sString The string that as to be shortened. I.e. 'The quick brown fox jumps over the lazy dog'
	 * @param Array   $aOptions Contains configuration options for the shorten logic. I. e.
	 *<code>
	 *                array(
	 *                   'max-length'          => 20,
	 *                   'ignore-word-borders' => true,
	 *                   'position'            => 'end',
	 *                   'ellipsis-characters' => '[...]'
	 *                )
	 * </code>
	 * <code>max-length</code>: Maximum length of the resulting string. May be shorter if word borders force it. Default is <code>15</code>.
	 * <code>ignore-word-borders</code>: Should words be cut off in the middle? Default is <code>false</code>.
	 * <code>position</code>: Where to remove characters? Possible values: <code>['end'|'middle'|'start']</code>. Default is <code>'end'</code>
	 * <code>ellipsis-characters</code>: Which characters should be used as placeholders? Default is <code>'...'</code>
	 * @return String Depending on additional parameters something like:
	 *			'The quick...'
	 *			'The quick br...' (<code>'ignore-word-borders' => true</code>)
	 *          'The ... dog' (<code>'position' => 'middle'</code>)
	 *          '... lazy dog' (<code>'position' => 'start'</code>)
	 */
	public static function shorten( $sString, $aOptions ) {
		$iMaxLength = BsCore::sanitizeArrayEntry( $aOptions, 'max-length', 15, BsPARAMTYPE::INT );
		$bIgnoreWordBorders = BsCore::sanitizeArrayEntry( $aOptions, 'ignore-word-borders', true,  BsPARAMTYPE::BOOL );
		$sPosition = BsCore::sanitizeArrayEntry( $aOptions, 'position', 'end', BsPARAMTYPE::STRING );
		$sEllipsisCharaters = BsCore::sanitizeArrayEntry( $aOptions, 'ellipsis-characters', '...', BsPARAMTYPE::STRING );

		if ( $iMaxLength <= 0 ) return $sString;

		$iGivenStringLength = mb_strlen( $sString );
		if ( $iGivenStringLength <= ( $iMaxLength + mb_strlen( $sEllipsisCharaters ) ) ) return $sString;

		$iEllipsisCharactersLength = mb_strlen( $sEllipsisCharaters );
		$iMaxLengthWithoutEllipsis = $iMaxLength - $iEllipsisCharactersLength;
		$sShortendString = '';

		if ( $bIgnoreWordBorders === true ) {
			switch ( $sPosition ) {
				case 'start':
					$sShortendString = $sEllipsisCharaters;
					$sShortendString .= mb_substr($sString, $iGivenStringLength - $iMaxLengthWithoutEllipsis );
					break;
				case 'middle':
					$iStartIndex = ceil( $iMaxLengthWithoutEllipsis / 2 ) ;
					$iEndIndex   = $iGivenStringLength - $iStartIndex - $iEllipsisCharactersLength;
					$sStart      = mb_substr( $sString, 0, $iStartIndex );
					$sEnd        = mb_substr( $sString, $iEndIndex );
					$sShortendString = $sStart.$sEllipsisCharaters.$sEnd;
					break;
				default:
					$sShortendString = mb_substr( $sString, 0, $iMaxLengthWithoutEllipsis );
					$sShortendString .= $sEllipsisCharaters;
					break;
			}
		} else {
			switch ( $sPosition ) {
				case 'start': // TODO RBV (12.10.10 12:18): implement
					break;
				case 'middle': // TODO RBV (12.10.10 12:18):  implement
					break;
				default:
					if( substr_count( $sString, ' ' ) == 0 ) {
						$sShortendString = mb_substr( $sString, 0, $iMaxLengthWithoutEllipsis );
						$sShortendString .= $sEllipsisCharaters;
						break;
					}
					$sWrappedString = wordwrap( $sString, $iMaxLength, '@@@' );
					$aWrappedStrings = explode( '@@@', $sWrappedString );
					$sShortendString = $aWrappedStrings[0].$sEllipsisCharaters;
					break;
			}
		}

		return $sShortendString;
	}

	/**
	 * Source: http://stackoverflow.com/questions/1369936/check-to-see-if-a-string-is-serialized
	 * Verifies if a variable is potentially serialized (stumbled upon this in wordpress) -- seems to work
	 * @param type $data
	 * @return boolean
	 */
	public static function isSerialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}
		return false;
	}

	const FILTER_STARTSWITH = 'sw';
	const FILTER_ENDSWITH = 'ew';
	const FILTER_CONTAINS = 'ct';
	const FILTER_CONTAINSNOT = 'nct';
	const FILTER_EQUALS = 'eq';
	const FILTER_EQUALSNOT = 'neq';

	const FILTER_LIKE = 'like';

	/**
	 * HINT: http://stackoverflow.com/a/10473026
	 * @param string $sOp ExtJS filter conformant operator<br/>
	 * 'sw' --> 'Starts with' <br/>
	 * 'ew' --> 'Ends with' <br/>
	 * 'ct' --> 'Contains' <br/>
	 * 'nct' --> 'Contains not' <br/>
	 * 'eq' --> 'Equals' <br/>
	 * 'neq' --> 'Not equals' <br/>
	 * @param string $sHaystack
	 * @param string $sNeedle
	 * @return bool
	 */
	public static function filter( $sOp, $sHaystack, $sNeedle, $bCaseSensitive = false ) {
		if( !$bCaseSensitive ) {
			$sHaystack = mb_strtolower( $sHaystack );
			$sNeedle = mb_strtolower( $sNeedle );
		}
		switch( $sOp ) {
			case self::FILTER_STARTSWITH:
				return $sNeedle === '' ||
					strrpos( $sHaystack, $sNeedle, - strlen( $sHaystack ) ) !== false;
			case self::FILTER_ENDSWITH:
				return $sNeedle === '' ||
					( ( $temp = strlen( $sHaystack ) - strlen( $sNeedle ) ) >= 0
					&& strpos( $sHaystack, $sNeedle, $temp ) !== false);
			case self::FILTER_CONTAINS:
			case self::FILTER_LIKE:
				return strpos( $sHaystack, $sNeedle ) !== false;
			case self::FILTER_CONTAINSNOT:
				return strpos( $sHaystack, $sNeedle ) === false;
			case self::FILTER_EQUALS:
				return $sHaystack === $sNeedle;
			case self::FILTER_EQUALSNOT:
				return $sHaystack !== $sNeedle;
		}
		return false;
	}

}
