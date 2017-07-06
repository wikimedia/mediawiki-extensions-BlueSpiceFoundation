<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @package bluespice
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2013-06-17 10:18:53 +0200 (Mo, 17 Jun 2013) $
 * $LastChangedBy: pwirth $
 * $Rev: 9757 $

 */

class ViewEditButtonPane extends ViewBaseElement {
	public function  __construct( $oI18N=null) {
		parent::__construct( $oI18N );
	}

	public function execute( $params = false ) {
		global $wgScriptPath;
		$sOut = '';
		$sOut .= '<div id="separator" style="height:1px;float:none;clear:both;"></div>'
			  .  '<div id="hw-toolbar" style="float:none;clear:both;">';
		$sOut .= parent::execute();
		$sOut .= '</div>';
		$sOut .=   '<div id="bs-editinfo-pane" style="clear:both;display:none;">';
		// CR MRG (18.06.11 12:11): i18n
		$sOut .=   '<img src="'.$wgScriptPath.'/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-ajax-loader-bar-blue.gif" style="padding: 10px;" alt="Loading..."/>';
		$sOut .= '</div>';
		return $sOut;
	}
}
