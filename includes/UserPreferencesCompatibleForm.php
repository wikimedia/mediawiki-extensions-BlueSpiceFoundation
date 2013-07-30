<?php

/**
 * blue spice for MediaWiki
 * Authors: Sebastian Ulbricht
 *
 * Copyright (C) 2010 Hallo Welt! � Medienwerkstatt GmbH, All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * For further information visit http://www.blue-spice.org
 *
 * Version information
 * $LastChangedDate: 2011-04-29 17:08:59 +0200 (Fr, 29 Apr 2011) $
 * $LastChangedBy: rvogel $
 * $Rev: 1801 $
 * $Id: UserPreferences.class.php 1801 2011-04-29 15:08:59Z rvogel $
 */
class UserPreferenceForm extends HTMLFormEx {
	function displayErrors( $errors ) {
		if ( is_array( $errors ) ) {
			$errorstr = $this->formatErrors( $errors );
		} else {
			$errorstr = $errors;
		}

		$errorstr = Html::rawElement( 'div', array( 'class' => 'error' ), $errorstr );

		return $errorstr;
	}

	function displayForm( $submitResult ) {
		$out = array(
			'errors' => NULL,
			'html' => NULL
		);

		if ( $submitResult !== false ) {
			$out['errors'] = $this->displayErrors( $submitResult );
		}

		$out['html'] = $this->getBody();

		return $out;
	}

	function show() {
		$html = '';

		self::addJS();

		# Load data from the request.
		$this->loadData();

		# Try a submission
		global $wgUser, $wgRequest;
		$editToken = $wgRequest->getVal( 'wpEditToken' );

		$result = false;
		if ( $wgUser->matchEditToken( $editToken ) )
			$result = $this->trySubmit();

		if( $result === true )
			return $result;

		# Display form.
		return $this->displayForm( $result );
	}
}