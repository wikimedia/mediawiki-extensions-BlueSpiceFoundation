<?php
/**
 * ResourceLoader class for manifest RL file modules for BlueSpice
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
 * @author     Patric Wirth, Robert Vogel
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2019 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 */
namespace BlueSpice\ResourceModule;

use BlueSpice\TemplateFactory;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\ResourceLoader\LessVars;

class FileModule extends LessVars {

	/**
	 * Takes named templates by the module and returns an array mapping.
	 * @return array Templates mapping template alias to content
	 * @throws MWException
	 */
	public function getTemplates() {
		$templates = [];
		foreach ( $this->templates as $alias => $templatePath ) {
			// Alias is optional
			if ( is_int( $alias ) ) {
				$alias = $templatePath;
			}
			$template = $this->getTemplateFactory()->get( $alias );
			$localPath = $template->getFilePath();
			if ( file_exists( $localPath ) ) {
				$content = file_get_contents( $localPath );
				$templates[$alias] = $this->stripBom( $content );
			} else {
				$msg = __METHOD__ . ": template file not found: \"$localPath\"";
				wfDebugLog( 'resourceloader', $msg );
				throw new \MWException( $msg );
			}
		}
		return $templates;
	}

	/**
	 *
	 * @return TemplateFactory
	 */
	protected function getTemplateFactory() {
		return MediaWikiServices::getInstance()->getService( 'BSTemplateFactory' );
	}
}
