<?php
/**
 * Hook handler base class for chameleon hook ChameleonSkinTemplateOutputPageBeforeExec
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
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use Config;
use IContextSource;
use QuickTemplate;
use SkinTemplate;

abstract class ChameleonSkinTemplateOutputPageBeforeExec extends Hook {
	/**
	 *
	 * @var SkinTemplate
	 */
	protected $skin = null;

	/**
	 *
	 * @var QuickTemplate
	 */
	protected $template = null;

	/**
	 *
	 * @param SkinTemplate $skin
	 * @param QuickTemplate $template
	 * @return bool
	 */
	public static function callback( $skin, $template ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$skin,
			$template
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param SkinTemplate $skin
	 * @param QuickTemplate $template
	 */
	public function __construct( $context, $config, $skin, $template ) {
		parent::__construct( $context, $config );

		$this->skin = $skin;
		$this->template = $template;
	}

	/**
	 *
	 * @param string $fieldName
	 * @param array $item
	 */
	protected function mergeSkinDataArray( $fieldName, $item ) {
		if ( !isset( $this->template->data[$fieldName] ) ) {
			$this->template->data[$fieldName] = [];
		}

		$this->template->data[$fieldName] = array_merge(
			$this->template->data[$fieldName],
			$item
		);
	}

	/**
	 *
	 * @param string $fieldName
	 * @param array $item
	 */
	protected function appendSkinDataArray( $fieldName, $item ) {
		if ( !isset( $this->template->data[$fieldName] ) ) {
			$this->template->data[$fieldName] = [];
		}

		$this->template->data[$fieldName][] = $item;
	}
}
