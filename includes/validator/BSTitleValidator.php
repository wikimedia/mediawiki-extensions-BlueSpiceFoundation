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
 * For further information visit https://bluespice.com
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @package    Bluespice_Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

use MediaWiki\Title\Title;

/**
 * BSTitleValidator class in BlueSpice
 * @package BlueSpice_Foundation
 */
class BSTitleValidator extends \ValueValidators\TitleValidator {

	/**
	 *
	 * @var array
	 */
	protected $aNamespaceBlacklist = [];

	/**
	 *
	 * @var bool
	 */
	protected $isAllowedEmpty = false;

	/**
	 *
	 * @param array $aNamespaceBlacklist
	 */
	public function setNamespaceBlacklist( $aNamespaceBlacklist ) {
		$this->aNamespaceBlacklist = $aNamespaceBlacklist;
	}

	/**
	 *
	 * @param bool $isAllowedEmpty
	 */
	public function setIsAllowedEmpty( $isAllowedEmpty = true ) {
		$this->isAllowedEmpty = $isAllowedEmpty;
	}

	/**
	 *
	 * @param Title $oTitle
	 */
	public function doValidation( $oTitle ) {
		if ( !$oTitle ) {
			if ( $this->isAllowedEmpty ) {
				return;
			}
			$this->addErrorMessage(
				wfMessage( 'bs-validator-invalid-string' )->plain()
			);
			return;
		}
		if ( $this->hasToExist && !$oTitle->exists() ) {
			$this->addErrorMessage(
				wfMessage(
					'bs-validator-error-title-does-not-exist',
					$oTitle->getPrefixedText()
				)->plain()
			);
		}

		if ( in_array( $oTitle->getNamespace(), $this->aNamespaceBlacklist ) ) {
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
		if ( isset( $options['isallowedempty'] ) ) {
			$this->setIsAllowedEmpty( $options['isallowedempty'] ? true : false );
		}
	}
}
