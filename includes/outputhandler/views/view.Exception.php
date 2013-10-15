<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Robert Vogel
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2012-09-12 16:55:09 +0200 (Mi, 12 Sep 2012) $
 * $LastChangedBy: smuggli $
 * $Rev: 6486 $

 */

class ViewException extends ViewBaseElement {
	protected $oException = null;

	public function  __construct( Exception $oException ) {
		parent::__construct();
		$this->oException = $oException;
	}

	function execute( $params = false ) {
		$aOut = array();
		$aOut[] = '<div class="bs-exception">';
		$aOut[] = '  <h3>' . wfMsg( 'bs-exception-view-heading' ) . '</h3>';
		$aOut[] = '  <p>' . wfMsg( 'bs-exception-view-text' ) . '</p>';
		$aOut[] = '  <div class="bs-exception-message">';
		$aOut[] = wfMsg( $this->oException->getMessage() );
		$aOut[] = '  </div>';
		$aOut[] = '  <p>' . wfMsg( 'bs-exception-view-admin-hint' ) . '</p>';
		$aOut[] = '  <hr />';
		$aOut[] = '  <span class="bs-exception-stacktrace-toggle">';
		$aOut[] = '    <span style="display:none;">' . wfMsg( 'bs-exception-view-stacktrace-toggle-show-text' ) . '</span>';
		$aOut[] = '    <span style="display:none;">' . wfMsg( 'bs-exception-view-stacktrace-toggle-hide-text' ) . '</span>';
		$aOut[] = '  </span>';
		$aOut[] = '  <div class="bs-exception-stacktrace">';
		$aOut[] = '   <pre>';
		$aOut[] = $this->oException->getTraceAsString();
		$aOut[] = '   </pre>';
		$aOut[] = '  </div>';
		$aOut[] = '</div>';
		return implode( "\n", $aOut );
	}
}