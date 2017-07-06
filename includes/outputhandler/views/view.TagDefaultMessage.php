<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2011-05-05 15:40:25 +0200 (Do, 05 Mai 2011) $
 * $LastChangedBy: mglaser $
 * $Rev: 1851 $

 */

class ViewTagDefaultMessage extends ViewBaseElement {

	protected $mDefaultMsg;
	protected $mSenderName;
	
	public function  __construct( $msg ) {
		parent::__construct();
		$this->mDefaultMsg = $msg;
	}

	// TODO MRG (01.09.10 02:00): Move sender name to tagerrorlist
	function execute( $params = false ) {
		$out = '';
		$out .= '<div class="bsTagDefaultMessage" >';
		$out .= $this->mDefaultMsg;
		$out .= '</div>';
		return $out;
	}
}