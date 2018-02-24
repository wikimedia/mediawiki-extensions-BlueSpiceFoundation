<?php
/**
 * This class contains helpful methods working with connections.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Marc Reymann, Robert Vogel
 *
 * $LastChangedDate: 2013-06-12 15:58:22 +0200 (Mi, 12 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9700 $

 */
class BsConnectionHelper {
	
	public static function urlExists( $sUrl, $iTimeout = 3 ) {
		$ctx = stream_context_create( array (
						'http' => array( 'timeout' => $iTimeout ) )
			);
		// TODO MRG (21.09.10 12:06): Performanz: würde hier ein Ping nicht reichen?
		// TODO MRG (21.09.10 12:06): Security: es erscheint mir sehr unsicher, Daten von
		// irgendwelchen Quellen hier einfach weiter zu reichen. ich schlage vor, content nur
		// wie oben vorgeschlagen, über eine separate Funktion weiterzugeben. Oder besser: zwei
		// Funktionen, einmal getContent und einmal getContentRaw. Letztere würde dem Reviewer
		// zeigen, dass hier besondere Vorsicht geboten ist.
		// TODO RBV (12.10.10 11:53): Die Funktion stammt aus dem Installcheck
		// bzw. den ersten Versionen des PdfExport. Ich habe sie so wie sie war
		// gekapselt. Ping kann nicht verwendet werden, da es hier um das
		// Vorhandensein einer Netzwerkressource über HTTP geht. Ping würde nur
		// sicherstellen dass da ein Gerät ist, aber nicht ob das "Dokument"
		// verfügbar ist. Außerdem geht es primär um gen Timeout.
		$sResponse = @file_get_contents( $sUrl, 0, $ctx );
		$bUrlExists = ( empty ( $sResponse) ) ? false : true;

		return $bUrlExists;
	}

	/**
	 *
	 * @param String $sUrl
	 * @param Float $fTimeout
	 * @return Boolean
	 */
	public static function testUrlForTimeout( $sUrl, $fTimeout = 3.0 ) {
		$iErrorNo = 0;
		$sErrorMsg = '';
		$aUrlInfo = parse_url($sUrl);
		if ( isset($aUrlInfo['scheme']) && $aUrlInfo['scheme'] == 'https' ) {
			$iPort = isset( $aUrlInfo['port'] ) ? $aUrlInfo['port'] : 443;
			@$pFile = fsockopen('ssl://' . $aUrlInfo['host'], $iPort, $iErrorNo, $sErrorMsg, $fTimeout );
		} else {
			$iPort = isset( $aUrlInfo['port'] ) ? $aUrlInfo['port'] : 80;
			@$pFile = fsockopen($aUrlInfo['host'], $iPort, $iErrorNo, $sErrorMsg, $fTimeout );
		}
		return (bool) $pFile;
	}
}
