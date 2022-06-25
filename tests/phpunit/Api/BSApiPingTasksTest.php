<?php

namespace BlueSpice\Tests\Api;

use BlueSpice\Tests\BSApiTasksTestBase;

/**
 * @group medium
 * @group API
 * @group Database
 * @group BlueSpice
 * @group BlueSpiceFoundation
 */
class BSApiPingTasksTest extends BSApiTasksTestBase {
	protected function getModuleName() {
		return 'bs-ping-tasks';
	}

	public function setUp(): void {
		global $wgHooks;
		parent::setUp();

		$this->insertPage( 'Test page', 'Dummy text' );

		$wgHooks['BsAdapterAjaxPingResult'] = [];
		$wgHooks['BsAdapterAjaxPingResult'][] = [ $this, 'onBsAdapterAjaxPingResult' ];
	}

	/**
	 * @covers \BSApiPingTasks::task_ping
	 */
	public function testPing() {
		$oTitle = \Title::makeTitle( NS_MAIN, 'Test page' );
		$oWikiPage = $this->getServiceContainer()->getWikiPageFactory()->newFromTitle( $oTitle );

		$oResponse = $this->executeTask(
			'ping',
			[
				'iArticleID' => $oTitle->getArticleID(),
				'iNamespace' => NS_MAIN,
				'sTitle' => $oTitle->getPrefixedText(),
				'iRevision' => $oWikiPage->getRevisionRecord()->getID(),
				'BsPingData' => [
					[
						'sRef' => 'DummyRef',
						'aData' => []
					]
				]
			]
		);

		$this->assertTrue( $oResponse->success, 'Entire ping task failed' );
		$this->assertArrayHasKey(
			'DummyRef',
			$oResponse->payload, "Response does not contain passed key"
		);
		$this->assertArrayHasKey(
			'success',
			$oResponse->payload['DummyRef'],
			"Single response does not contain key 'success'"
		);
		$this->assertTrue(
			$oResponse->payload['DummyRef']['success'],
			"Single ping task returned false"
		);
	}

	/**
	 * Dummy hook handler for BsAdapterAjaxPingResult
	 * @param string $sRef
	 * @param array $aData
	 * @param int $iArticleId
	 * @param string $sTitle
	 * @param int $iNamespace
	 * @param int $iRevision
	 * @param array &$aSingleResult
	 * @return bool
	 */
	public static function onBsAdapterAjaxPingResult( $sRef, $aData, $iArticleId, $sTitle,
		$iNamespace, $iRevision, &$aSingleResult ) {
		$oTitle = \Title::makeTitle( NS_MAIN, 'Test page' );

		if ( $iArticleId != $oTitle->getArticleID() ) {
			return false;
		}
		if ( $sRef != 'DummyRef' ) {
			return false;
		}

		$aSingleResult['success'] = true;
		return true;
	}
}
