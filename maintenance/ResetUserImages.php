<?php

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\MediaWikiServices;

require_once __DIR__ . '/Maintenance.php';

class ResetUserImages extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( "Resets all user images" );
		$this->requireExtension( 'BlueSpiceFoundation' );
	}

	public function execute() {
		$dbw = $this->getDB( DB_PRIMARY );
		$res = $dbw->select(
			'user',
			'user_id',
			'',
			__METHOD__
		);
		$userFactory = MediaWikiServices::getInstance()->getUserFactory();
		foreach ( $res as $row ) {
			$oUser = $userFactory->newFromId( $row->user_id );
			$sUserImage = $oUser->getName() . '.jpg';

			$dbw->delete(
				'user_properties',
				[
					'up_user' => $oUser->getId(),
					'up_property' => 'MW::UserImage'
				],
				__METHOD__
			);
			$dbw->insert(
				'user_properties',
				[
					'up_user' => $oUser->getId(),
					'up_property' => 'MW::UserImage',
					'up_value' => serialize( $sUserImage )
				],
				__METHOD__,
				'IGNORE'
			);
			echo 'Reseted user ' . $oUser->getName() . ' new value ' . $sUserImage . "\n";
		}
	}
}

$maintClass = ResetUserImages::class;
require_once RUN_MAINTENANCE_IF_MAIN;
