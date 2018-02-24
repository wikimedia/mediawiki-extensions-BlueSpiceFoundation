<?php
/**
 * Provides the ping task.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 */

/**
 * TODO: Works for now but needs refactoring soon!
 * Provides the ping task.
 * @package BlueSpice_Foundation
 */
class BSApiPingTasks extends BSApiTasksBase {
	protected $aTasks = array(
		'ping' => [
			'examples' => [
				[
					'iArticleID' => 543,
					'iNamespace' =>  10,
					'sTitle' => 'Some page',
					'iRevision' => 324,
					'BsPingData' => [
						[
							'sRef' => 'Reference',
							'aData' => []
						]
					]
				]
			],
			'params' => [
				'iArticleID' => [
					'desc' => 'A valid page id',
					'type' => 'integer',
					'required' => false
				],
				'iNamespace' => [
					'desc' => 'A valid namespace id',
					'type' => 'integer',
					'required' => false
				],
				'sTitle' => [
					'desc' => 'A valid page title',
					'type' => 'string',
					'required' => false
				],
				'iRevision' => [
					'desc' => 'A valid revision id',
					'type' => 'integer',
					'required' => false
				],
				'BsPingData' => [
					'desc' => 'Array of objects that contain the actual ping data packages',
					'type' => 'array',
					'required' => true
				],
			]
		]
	);

	protected $aReadTasks = array( 'ping' );

	/**
	 * Configures the global permission requirements
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return array(
			'ping' => array( 'read' )
		);
	}

	protected function task_ping( $oTaskData ) {
		$oResponse = $this->makeStandardReturn();

		//TODO: Params need very hard param processing!
		$iArticleId = isset( $oTaskData->iArticleID )
			? (int) $oTaskData->iArticleID
			: 0
		;
		$iNamespace = isset( $oTaskData->iNamespace )
			? (int) $oTaskData->iNamespace
			: 0
		;
		$sTitle = isset( $oTaskData->sTitle )
			? (string) $oTaskData->sTitle
			: ''
		;
		$iRevision = isset( $oTaskData->iRevision )
			? (int) $oTaskData->iRevision
			: 0
		;
		$aBSPingData = isset( $oTaskData->BsPingData )
			? (array) $oTaskData->BsPingData
			: array()
		;

		$oResponse->success = true;
		$oResponse->payload = array();
		if( empty($aBSPingData) ) {
			return $oResponse;
		}

		foreach( $aBSPingData as $oSinglePing ) {
			if( !$oResponse->success ) {
				break;
			}
			if( empty($oSinglePing) || empty($oSinglePing->sRef) ) {
				continue;
			}

			//Workaround: Each hook handler expect an array
			$aSinglePing = (array) $oSinglePing;
			if( !isset($aSinglePing['aData']) ) {
				$aSinglePing['aData'] = array();
			} else {
				//TODO: Each data set needs very hard param processing too!
				$aSinglePing['aData'] = (array) $aSinglePing['aData'];
			}
			//Workaround: Each hook handler expect an array not an object
			foreach( $aSinglePing['aData'] as $iKey => $oData ) {
				if( empty($oData) ) {
					$aSinglePing['aData'][$iKey] = array();
					continue;
				}
				if( !$oData instanceof stdClass ) {
					continue;
				}
				$aSinglePing['aData'][$iKey] = (array) $oData;
			}

			$aSingleResult = array(
				"success" => false,
				"errors" => array(),
				"message" => '',
			);
			//if hook returns false - overall success is false
			$oResponse->success = Hooks::run( 'BsAdapterAjaxPingResult', array(
				$aSinglePing['sRef'],
				$aSinglePing['aData'],
				$iArticleId,
				$sTitle,
				$iNamespace,
				$iRevision,
				&$aSingleResult
			));
			$oResponse->payload[$aSinglePing['sRef']] = $aSingleResult;
		}

		return $oResponse;
	}
}
