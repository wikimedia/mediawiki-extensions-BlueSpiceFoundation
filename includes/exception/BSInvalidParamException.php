<?php
/**
 * Exception thrown and processed in case a param processing resulted in an
 * error
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
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
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
