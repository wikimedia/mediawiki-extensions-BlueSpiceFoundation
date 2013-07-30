<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @package bluespice
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2013-06-13 11:09:22 +0200 (Do, 13 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9725 $
 * $Id: view.EditButton.php 9725 2013-06-13 09:09:22Z rvogel $
 */

class ViewEditButton extends ViewBaseElement {
	protected $mId;
	protected $mOnClick;
	protected $mImage;
	protected $mMsg;

	public function  __construct( $I18N=null ) {
		parent::__construct( $I18N );
	}

	public function execute( $params = false ) {

		$sOut  = $this->renderButton();
		return $sOut;
	}

	public function setId( $id ) {
		$this->mId = $id;
	}

	public function setOnClick( $onClick ) {
		$this->mOnClick = $onClick;
	}

	public function setImage( $image ) {
		$this->mImage = $image;
	}

	public function setMsg( $msg ) {
		$this->mMsg = $msg;
	}

	protected function renderButton() {
		$aOut = array();

		$aOut[] = '<div style="float:left">';
		$aOut[] = '  <div id="'.$this->mId.'" class="hw-button-a" onclick="'.$this->mOnClick.'">';
		$aOut[] = '    <div class="hw-button-left">';
		$aOut[] = '      <img src="'.BsCore::getInstance('MW')->getAdapter()->get('ScriptPath').$this->mImage.'" class="hw-button-img" alt="'.$this->mMsg.'">';
		$aOut[] ='     </div>';
		$aOut[] = '    <div class="hw-button-middle">'.$this->mMsg.'</div>';
		$aOut[] = '    <img src="'.BsCore::getInstance('MW')->getAdapter()->get('ScriptPath').'/extensions/BlueSpiceFoundation/resources/bluespice/images/btn_right.gif" class="hw-button-right" alt="'.$this->mMsg.'">';
		$aOut[] = '  </div>';
		$aOut[] = '</div>';

		$sOut = implode("\n", $aOut);
		return $sOut;
	}
}
