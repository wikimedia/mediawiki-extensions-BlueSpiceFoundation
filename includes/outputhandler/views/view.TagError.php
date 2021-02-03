<?php
/**
 * Renders a tag error message.
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Markus Glaser <glaser@hallowelt.com>, Sebastian Ulbricht
 *
 * @package    BlueSpice_Core
 * @subpackage Views
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewTagError extends ViewBaseElement {

	protected $mErrorMsg;
	protected $mSenderName;

	/**
	 *
	 * @param string $msg
	 */
	public function __construct( $msg ) {
		parent::__construct();
		$this->mLineFeed = '<br />';
		$this->mErrorMsg = $msg;
	}

	/**
	 * TODO MRG (01.09.10 02:00): Move sender name to tagerrorlist
	 * @param array|false $params
	 * @return string
	 */
	public function execute( $params = false ) {
		$out = '';
		// TODO MRG (01.09.10 02:01): remove style attribute
		$out .= '<div class="bsTagError">';
		$out .= $this->mErrorMsg;
		$out .= '</div>';
		return $out;
	}
}
