<?php

/**
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH
 * @author Robert Vogel <vogel@hallowelt.com>
 */

require_once( 'BSMaintenance.php' );

class DeployJavaWebApps extends BSMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption('target', 'The path to the Jave Application Server\'s "webapps" directory', true, true);
		$this->addOption('baseURL', 'The url the Java Application Server can be accessed with', false, true);
	}

	public function execute() {
		global $wgPasswordSender;
		
		$sTarget  = $this->getOption( 'target' );
		$sBaseURL = $this->getOption( 'baseURL' );

		$sRealTarget = realpath( $sTarget );

		//TODO: Check write permission to $sRealTarget
		//TODO: Check accessablility of $sBaseURL

		$aReport = array();
		
		Hooks::run('BSDeployJavaWebApps', array($this, $sRealTarget, $sBaseURL, &$aReport ) );

		$this->output( 'Deployment done.' );
		$this->output( implode( "\n", $aReport ) );

		$this->output( 'Deployment done.' );
	}
}

$maintClass = 'DeployJavaWebApps';
require_once RUN_MAINTENANCE_IF_MAIN;
