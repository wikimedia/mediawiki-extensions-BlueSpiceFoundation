<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser
 *
 * $LastChangedDate: 2013-02-26 16:27:57 +0100 (Di, 26 Feb 2013) $
 * $LastChangedBy: pwirth $
 * $Rev: 8734 $

 */
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewTagErrorList extends ViewBaseElement {

	protected $mSenderName;

	public function __construct( $sender = null ) {
		parent::__construct();
		if ($sender && ( $sender instanceof BsExtensionMW )) {
			$this->mSenderName = $sender->getName();
		}
	}


	public function hasEntries() {
		return count($this->_mItems);
	}

	public function execute( $params = false ) {
		$out = '<fieldset class="bsErrorFieldset">';
		$out .= '<legend class="bsErrorLegend">'.wfMessage('bs-viewtagerrorlist-legend', $this->mSenderName)->plain().'</legend>';
		$out .= parent::execute();
		$out .= '</fieldset>';
		return $out;
	}
}
