<?php

class BsExtJSHelper {
	/* 
	 */

	protected static $aTreeReferences = array();

	/**
	 * Build an json-object for Ext.tree with the given nodes.
	 * Lilu:
	 * json_encode könnte theoretisch verwendet werden. Allerdings bräuchten wird dann zusätzlich eine rekursiv ausführbare
	 * Helfer-Methode, die ein PHP-Array mit den benötigten Daten aufbaut, welches anschliessend von buildTree mit json_encode
	 * umgewandelt wird. Ich denke, dies wäre wesentlich unperformanter und auch speicherintensiver, da die Daten dann doppelt
	 * gehalten werden müssten und für Arrays zusätzlich Indizes im Speicher gehalten werden müssten.
	 * @param array $nodes
	 * @return string A stringified JavaScript object object
	 */
	public static function buildTree($nodes) {

		$iBreakLevel = BsConfig::get('MW::RekursionBreakLevel');
		$aOut = array();
		foreach ($nodes as $node => $data) {
			if (isset(self::$aTreeReferences[$data['id']]) && self::$aTreeReferences[$data['id']] > $iBreakLevel) {
				return '[{ "id":"0", "text":"+++ RECURSION +++", "leaf":true }]';
			} else if (!isset(self::$aTreeReferences[$data['id']])) {
				self::$aTreeReferences[$data['id']] = 0;
			}
			self::$aTreeReferences[$data['id']]++;
			$tmp = '"id":"' . $data['id'] . '","text":"' . $data['name'] . '",';
			if (is_array($data['children'])) {
				$tmp .= '"children":' . self::buildTree($data['children']);
			} else {
				$tmp .= '"leaf":true';
			}
			$aOut[] = $tmp;
		}

		if (!count($aOut)) {
			return '[]';
		}
		return '[{' . implode('},{', $aOut) . '}]';
	}

}