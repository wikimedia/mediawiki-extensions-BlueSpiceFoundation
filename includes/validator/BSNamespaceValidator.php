<?php
/**
 * Validates a namespace id based on various settings
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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * BSNamespaceValidator class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSNamespaceValidator extends \ValueValidators\ValueValidatorObject {

	/**
	 *
	 * @var boolean
	 */
	protected $hasToExist = false;

	/**
	 *
	 * @var array
	 */
	protected $aBlacklist = array();

	/**
	 *
	 * @param boolean $hasToExist
	 */
	public function setHasToExist( $hasToExist ) {
		$this->hasToExist = $hasToExist;
	}

	/**
	 *
	 * @param array $aBlacklist
	 */
	public function setBlacklist( $aBlacklist ) {
		$this->aBlacklist = $aBlacklist;
	}

	public function doValidation( $value ) {

		//TODO: finalize implementation
		if( $this->hasToExist && !MWNamespace::exists( $value ) ) {
			$this->addErrorMessage(
				wfMessage(
					'bs-validator-error-namespace-does-not-exist',
					$value
				)->plain()
			);
		}

		if( in_array( $value, $this->aBlacklist ) ) {
			$this->addErrorMessage(
				wfMessage(
					'bs-validator-error-namespace-on-blacklist',
					$value
				)->plain()
			);
		}
	}

	/**
	 *
	 * @param array $options
	 */
	public function setOptions( array $options ) {
		parent::setOptions( $options );

		if ( isset( $options['hastoexist'] ) ) {
			$this->setHasToExist( $options['hastoexist'] );
		}

		if ( isset( $options['blacklist'] ) ) {
			$this->setBlacklist( $options['blacklist'] );
		}
	}
}
