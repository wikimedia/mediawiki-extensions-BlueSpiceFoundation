<?php

require_once( __DIR__ . '/Maintenance.php' );

class ResetUserImages extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Resets all user images";
		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	function execute() {
		$dbw = wfGetDB( DB_MASTER );
		$res = $dbw->select( 'user', 'user_id' );
		while ( $row = $dbw->fetchObject( $res ) ) {
			$oUser = User::newFromId( $row->user_id );
			$sUserImage = $oUser->getName().'.jpg';

			$dbw->delete(
				'user_properties',
				array(
					'up_user' => $oUser->getId(),
					'up_property' => 'MW::UserImage'
				)
			);
			$dbw->insert(
				'user_properties',
				array(
					'up_user' => $oUser->getId(),
					'up_property' => 'MW::UserImage',
					'up_value' => serialize( $sUserImage )
				),
				__METHOD__,
				'IGNORE'
			);
			echo 'Reseted user ' . $oUser->getName() . ' new value '. $sUserImage. "\n";
		}
	}
}

$maintClass = "ResetUserImages";
require_once( RUN_MAINTENANCE_IF_MAIN );
