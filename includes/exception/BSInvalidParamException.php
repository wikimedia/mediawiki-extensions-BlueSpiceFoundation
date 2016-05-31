<?php
/**
 * Exception thrown and processed in case a param processing resulted in an
 * error
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
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * BSInvalidParamException class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSInvalidParamException extends Exception {
	/**
	 *
	 * @var \ParamProcessor\ProcessingError[]
	 */
	protected $aErrors = array();

	/**
	 *
	 * @param \ParamProcessor\ProcessingError[] $aErrors
	 */
	public function setErrors( $aErrors ) {
		$this->aErrors = $aErrors;
	}

	/**
	 *
	 * @return \ParamProcessor\ProcessingError[]
	 */
	public function getErrors() {
		return $this->aErrors;
	}
}