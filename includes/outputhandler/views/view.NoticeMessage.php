<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @abstract
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $

 */

// Last review: MRG20100816


// TODO MRG20100816: Wäre es hier nicht sinnvoller, inline und block als option zu verwenden?
class ViewNoticeMessage extends ViewBaseMessage {
	public function  __construct() {
		parent::__construct();
		$this->_mAutoWrap = '<div class="notice">###CONTENT###</div>';
	}
}
