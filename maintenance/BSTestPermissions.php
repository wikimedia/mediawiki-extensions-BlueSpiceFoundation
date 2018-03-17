<?php

require_once( 'BSMaintenance.php' );

class BSTestPermissions extends BSMaintenance {

	/**
	 *
	 * @var User
	 */
	protected $testUser = null;

	/**
	 *
	 * @var Title
	 */
	protected $testTitle = null;

	public function __construct() {
		parent::__construct();

		$this->addOption( 'username', 'The user to test with', true, true );
		$this->addOption( 'title', 'The page title to test against', false );
		$this->addOption( 'permission', 'The permission to test with', false, false );
	}

	public function execute() {
		$this->makeUser();
		$this->makeTitle();
		$this->outputTestParameters();
		$this->testPermission();
	}

	protected function makeUser() {
		$userName = $this->getOption( 'username' );
		$this->testUser = User::newFromName( $userName );
		if( $this->testUser instanceof User === false ) {
			throw new Exception( "Could not create valid user from '$userName'" );
		}
	}

	protected function makeTitle() {
		$title = $this->getOption( 'title' );
		$this->testTitle = Title::newFromText( $title );
		if( $this->testTitle instanceof Title === false ) {
			$this->testTitle = Title::newMainPage();
		}
	}

	protected function outputTestParameters() {
		$this->output( "--------------------------------------------------------------------------------" );
		$this->output( sprintf(
			"User: %s (ID:%d) | Title: %s (ID:%d) | Permission: %s",
			$this->testUser->getName(),
			$this->testUser->getId(),
			$this->testTitle->getPrefixedDBkey(),
			$this->testTitle->getArticleId(),
			$title = $this->getOption( 'permission' )

		) );
	}

	protected function testPermission() {
		RequestContext::getMain()->setUser( $this->testUser );
		$this->output( "--------------------------------------------------------------------------------" );
		$permission = $this->getOption( 'permission' );
		$result = FormatJson::encode(
			$this->testTitle->userCan( $permission, $this->testUser )
		);

		$this->output( "Result: $result" );
	}

}

$maintClass = 'BSTestPermissions';
require_once RUN_MAINTENANCE_IF_MAIN;
