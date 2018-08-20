<?php
/**
 * This file is part of BlueSpice MediaWiki.
 *
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @author Robert Vogel
 *
 * $LastChangedDate: 2012-09-12 16:55:09 +0200 (Mi, 12 Sep 2012) $
 * $LastChangedBy: smuggli $
 * $Rev: 6486 $

 */
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
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
		$aOut[] = '  <h3>' . wfMessage( 'bs-exception-view-heading' )->plain() . '</h3>';
		$aOut[] = '  <p>' . wfMessage( 'bs-exception-view-text' )->text() . '</p>';
		$aOut[] = '  <div class="bs-exception-message">';
		$aOut[] = wfMessage( $this->oException->getMessage() )->text();
		$aOut[] = '  </div>';
		$aOut[] = '  <p>' . wfMessage( 'bs-exception-view-admin-hint' )->text() . '</p>';
		$aOut[] = '  <hr />';
		$aOut[] = '  <span class="bs-exception-stacktrace-toggle">';
		$aOut[] = '    <span style="display:none;">' . wfMessage( 'bs-exception-view-stacktrace-toggle-show-text' )->text() . '</span>';
		$aOut[] = '    <span style="display:none;">' . wfMessage( 'bs-exception-view-stacktrace-toggle-hide-text' )->text() . '</span>';
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
