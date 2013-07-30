<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2013-02-26 16:27:57 +0100 (Di, 26 Feb 2013) $
 * $LastChangedBy: pwirth $
 * $Rev: 8734 $
 * $Id: view.TagErrorList.php 8734 2013-02-26 15:27:57Z pwirth $
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