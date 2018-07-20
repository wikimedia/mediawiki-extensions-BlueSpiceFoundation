<?php
/**
 * Hook handler base class for MediaWiki hook AuthChangeFormFields
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
 * @author     Dejan Savuljesku <savuljesku@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class AuthChangeFormFields extends Hook {

	/**
	 *
	 * @var array
	 */
	protected $requests = null;

	/**
	 *
	 * @var array
	 */
	protected $fieldInfo = null;

	/**
	 *
	 * @var array
	 */
	protected $formDescriptor = null;

	/**
	 *
	 * @var string
	 */
	protected $action = null;

	/**
	 *
	 * @param array $requests
	 * @param array $fieldInfo
	 * @param array $formDescriptor
	 * @param string $action
	 * @return boolean
	 */
	public static function callback( $requests, $fieldInfo, &$formDescriptor, $action ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$requests,
			$fieldInfo,
			$formDescriptor,
			$action
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param array $requests
	 * @param array $fieldInfo
	 * @param array $formDescriptor
	 * @param string $action
	 */
	public function __construct( $context, $config, $requests, $fieldInfo, &$formDescriptor, $action ) {
		parent::__construct( $context, $config );

		$this->requests = $requests;
		$this->fieldInfo = $fieldInfo;
		$this->formDescriptor = &$formDescriptor;
		$this->action = $action;
	}
}
