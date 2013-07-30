<?php

/**
 * This file contains the BsConfig class.
 *
 * The BsConfig class manages all settings for the BlueSpice framework and any
 * in the framework used adaptersettings.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
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
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @version    1.20.0
 * @version    $Id: RemoteAction.class.php 9725 2013-06-13 09:09:22Z rvogel $
 * @package    Bluespice_Core
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
// TODO SU (27.06.11 14:46): Core contaminations entfernen (evtl. hookähnliches methoden in jeweiligen adapter implementieren)

/**
 * the BsRemoteAction class
 * @package BlueSpice_Core
 * @subpackage Core
 */
class BsRemoteAction extends FormlessAction {

	/**
	 * Return the name of the action this object responds to
	 * @return String lowercase
	 */
	public function getName() {
		return 'remote';
	}

	/**
	 * Show something on GET request.
	 * @return String
	 */
	function onView() {
		global $wgGroupPermissions, $wgSquidMaxage, $wgForcedRawSMaxage, $wgJsMimeType;

		$this->getOutput()->disable();
		$request = $this->getRequest();

		$response = $request->response();

		$response->header( 'Content-type: text/html; charset=UTF-8' );
		# Output may contain user-specific data;
		# vary generated content for open sessions on private wikis
		$privateCache = !$wgGroupPermissions['*']['read'] && ( session_id() != '' );
		# allow the client to cache this for 24 hours
		$mode = $privateCache ? 'private' : 'public';
		$response->header( 'Cache-Control: ' . $mode . ', s-maxage=0, max-age=0' );

		#$text = $this->getRawText();
		$text = BsCore::getInstance( 'MW' )->getAdapter()->getRemoteActionContent( $this->getOutput() );

		echo $text;
	}

	/**
	 * Whether this action requires the wiki not to be locked
	 * @return Bool
	 */
	public function requiresWrite() {
		return false;
	}

	public function getRestriction() {}
}