<?php
/**
 * Validates a Title object based on various settings
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
 * BSTitleValidator class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSTitleValidator extends \ValueValidators\TitleValidator {

	/**
	 *
	 * @var array
	 */
	protected $aNamespaceBlacklist = array();

	/**
	 *
	 * @param array $aNamespaceBlacklist
	 */
	public function setNamespaceBlacklist( $aNamespaceBlacklist ) {
		$this->aNamespaceBlacklist = $aNamespaceBlacklist;
	}

	/**
	 *
	 * @param Title $oTitle
	 */
	public function doValidation( $oTitle ) {
		if( $this->hasToExist && !$oTitle->exists() ) {
			$this->addErrorMessage(
				wfMessage(
					'bs-validator-error-title-does-not-exist',
					$oTitle->getPrefixedText()
				)->plain()
			);
		}

		if( in_array( $oTitle->getNamespace(), $this->aNamespaceBlacklist ) ) {
			$this->addErrorMessage(
				wfMessage(
					'bs-validator-error-title-namespace-on-blacklist',
					$oTitle->getPrefixedText()
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

		if ( isset( $options['namespaceblacklist'] ) ) {
			$this->setNamespaceBlacklist( $options['namespaceblacklist'] );
		}
	}
}
