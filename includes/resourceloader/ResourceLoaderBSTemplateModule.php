<?php
/**
 * ResourceLoader class for BSTemplates resource module for BlueSpice
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
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

class ResourceLoaderBSTemplateModule extends ResourceLoaderModule {
	/**
	 * Takes named BlueSpice templates by the module and returns an array mapping.
	 * @return array of templates mapping template alias to content
	 * @throws MWException
	 */
	public function getTemplates() {
		$templates = parent::getTemplates();
		foreach( BSTemplateHelper::getAllTemplates() as $sName => $sPath ) {
			if ( is_int( $sName ) ) {
				continue;
			}
			if ( file_exists( $sPath ) ) {
				$content = file_get_contents( $sPath );
				$ext = pathinfo( $sPath, PATHINFO_EXTENSION );
				$templates["$sName.$ext"] = $this->stripBom( $content );
			} else {
				$msg = __METHOD__ . ": template file not found: \"$sPath\"";
				wfDebugLog( 'resourceloader', $msg );
				throw new MWException( $msg );
			}
		}
		return $templates;
	}

	/**
	 * Takes an input string and removes the UTF-8 BOM character if present
	 *
	 * We need to remove these after reading a file, because we concatenate our files and
	 * the BOM character is not valid in the middle of a string.
	 * We already assume UTF-8 everywhere, so this should be safe.
	 *
	 * @return string input minus the intial BOM char
	 */
	protected function stripBom( $input ) {
		if ( substr_compare( "\xef\xbb\xbf", $input, 0, 3 ) === 0 ) {
			return substr( $input, 3 );
		}
		return $input;
	}

	/**
	 * Get target(s) for the module, eg ['desktop'] or ['desktop', 'mobile']
	 *
	 * @return array Array of strings
	 */
	public function getTargets() {
		return [ 'desktop', 'mobile' ];
	}

	/**
	 * Get a list of modules this module depends on.
	 *
	 * Dependency information is taken into account when loading a module
	 * on the client side.
	 *
	 * Note: It is expected that $context will be made non-optional in the near
	 * future.
	 *
	 * @param ResourceLoaderContext|null $context
	 * @return array List of module names as strings
	 */
	public function getDependencies(\ResourceLoaderContext $context = null) {
		return array_merge(
			parent::getDependencies( $context ),
			[ "mediawiki.template.mustache" ]
		);
	}
}
