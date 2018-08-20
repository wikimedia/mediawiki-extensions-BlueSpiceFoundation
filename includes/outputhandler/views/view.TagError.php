<?php
/**
 * Renders a tag error message.
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Markus Glaser <glaser@hallowelt.com>, Sebastian Ulbricht

 * @package    BlueSpice_Core
 * @subpackage Views
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewTagError extends ViewBaseElement {

	protected $mErrorMsg;
	protected $mSenderName;

	public function  __construct( $msg ) {
		parent::__construct();
		$this->mLineFeed = '<br />';
		$this->mErrorMsg = $msg;
	}

	// TODO MRG (01.09.10 02:00): Move sender name to tagerrorlist
	function execute( $params = false ) {
		$out = '';
		// TODO MRG (01.09.10 02:01): remove style attribute
		$out .= '<div class="bsTagError">';
		$out .= $this->mErrorMsg;
		$out .= '</div>';
		return $out;
	}
}
