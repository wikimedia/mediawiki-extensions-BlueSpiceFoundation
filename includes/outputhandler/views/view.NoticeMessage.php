<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $
 * $Id: ViewManager.class.php 314 2010-07-17 23:13:04Z mglaser $
 */

// Last review: MRG20100816


// TODO MRG20100816: Wäre es hier nicht sinnvoller, inline und block als option zu verwenden?
class ViewNoticeMessage extends ViewBaseMessage {
	public function  __construct() {
		parent::__construct();
		$this->_mAutoWrap = '<div class="notice">###CONTENT###</div>';
	}
}