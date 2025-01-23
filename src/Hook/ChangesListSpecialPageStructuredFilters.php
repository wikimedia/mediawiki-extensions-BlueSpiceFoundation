<?php
/**
 * Hook handler base class for MediaWiki hook ChangesListSpecialPageStructuredFilters
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
 * @author     Oleksandr Pinchuk <intracomof@gmail.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use ChangesListSpecialPage;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class ChangesListSpecialPageStructuredFilters extends Hook {

	/** @var ChangesListSpecialPage */
	protected $specialPage;

	/**
	 * @param ChangesListSpecialPage $specialPage
	 * @return bool
	 */
	public static function callback( ChangesListSpecialPage $specialPage ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$specialPage
		);
		return $hookHandler->process();
	}

	/**
	 * AddSocialEntityFilter constructor.
	 * @param IContextSource $context
	 * @param Config $config
	 * @param ChangesListSpecialPage $specialPage
	 */
	public function __construct( $context, $config, ChangesListSpecialPage $specialPage ) {
		parent::__construct( $context, $config );
		$this->specialPage = $specialPage;
	}
}
