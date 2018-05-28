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

	public function setUp() {
		global $wgHooks;
		parent::setUp();

		$this->insertPage( 'Test page', 'Dummy text' );

		$wgHooks['BsAdapterAjaxPingResult'] = [];
		$wgHooks['BsAdapterAjaxPingResult'][] = array( $this, 'onBsAdapterAjaxPingResult' );
	}

	public function testPing() {
		$oTitle = \Title::makeTitle( NS_MAIN, 'Test page' );
		$oWikiPage = \WikiPage::factory( $oTitle );

		$oResponse = $this->executeTask(
			'ping',
			[
				'iArticleID' => $oTitle->getArticleID(),
				'iNamespace' =>  NS_MAIN,
				'sTitle' => $oTitle->getPrefixedText(),
				'iRevision' => $oWikiPage->getRevision()->getID(),
				'BsPingData' => [
					[
						'sRef' => 'DummyRef',
						'aData' => []
					]
				]
			]
		);

		$this->assertTrue( $oResponse->success, 'Entire ping task failed' );
		$this->assertArrayHasKey( 'DummyRef', $oResponse->payload, "Response does not contain passed key" );
		$this->assertArrayHasKey( 'success', $oResponse->payload['DummyRef'], "Single response does not contain key 'success'" );
		$this->assertTrue( $oResponse->payload['DummyRef']['success'], "Single ping task returned false" );
	}

	/**
	 * Dummy hook handler for BsAdapterAjaxPingResult
	 * @param string $sRef
	 * @param array $aData
	 * @param int $iArticleId
	 * @param string $sTitle
	 * @param integer $iNamespace
	 * @param integer $iRevision
	 * @param array $aSingleResult
	 */
	public static function onBsAdapterAjaxPingResult ( $sRef, $aData, $iArticleId, $sTitle, $iNamespace, $iRevision, &$aSingleResult) {
		$oTitle = \Title::makeTitle( NS_MAIN, 'Test page' );

		if( $iArticleId != $oTitle->getArticleID() ) {
			return false;
		}
		if( $sRef != 'DummyRef' ) {
			return false;
		}

		$aSingleResult['success'] = true;
		return true;
	}
}
