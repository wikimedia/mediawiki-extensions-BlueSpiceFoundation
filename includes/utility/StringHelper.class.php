<?php
/**
 * This class contains helpful methods for processing strings.
 *
 * @copyright Copyright (c) 2007-2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Robert Vogel
 * @version 0.1.0 beta
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
		//wfProfileIn( 'BS::'.__METHOD__ );

		$iMaxLength         = BsCore::sanitizeArrayEntry( $aOptions, 'max-length',          15,    BsPARAMTYPE::INT );
		$bIgnoreWordBorders = BsCore::sanitizeArrayEntry( $aOptions, 'ignore-word-borders', true,  BsPARAMTYPE::BOOL );
		$sPosition          = BsCore::sanitizeArrayEntry( $aOptions, 'position',            'end', BsPARAMTYPE::STRING );
		$sEllipsisCharaters = BsCore::sanitizeArrayEntry( $aOptions, 'ellipsis-characters', '...', BsPARAMTYPE::STRING );

		if ( $iMaxLength <= 0 ) return $sString;

		$iGivenStringLength = mb_strlen( $sString );
		if ( $iGivenStringLength <= ( $iMaxLength + mb_strlen( $sEllipsisCharaters ) ) ) return $sString;

		$iEllipsisCharactersLength = mb_strlen( $sEllipsisCharaters );
		$iMaxLengthWithoutEllipsis = $iMaxLength - $iEllipsisCharactersLength;
		$sShortendString           = '';

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
		}
		else {
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
		//wfProfileOut( 'BS::'.__METHOD__ );
		return $sShortendString;
	}
}
