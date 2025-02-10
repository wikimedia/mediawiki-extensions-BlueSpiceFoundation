<?php
/**
 * BSTemplateHelper class for BlueSpice
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
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;

/**
 * @package BlueSpiceFoundation
 */
class BSTemplateHelper {
	protected static $sTemplatePath = 'resources/templates';
	protected static $sSeparator = '.';
	protected static $sFileExt = '.mustache';

	/**
	 *
	 * @param string $sExtName
	 * @param string $sFullPath
	 * @return string
	 * @throws BsException
	 */
	protected static function makeTemplateNameFromPath( $sExtName, $sFullPath ) {
		$sFullPath = BsFileSystemHelper::normalizePath( $sFullPath );
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		$aExtensions = $registry->getExtensionDefinitions();
		foreach ( ExtensionRegistry::getInstance()->getAllThings() as $thing ) {
			if ( !isset( $thing['type'] ) || $thing['type'] !== 'skin' ) {
				continue;
			}
			if ( $thing['name'] !== $sExtName ) {
				continue;
			}
			$aExtensions[$thing['name']]['extPath'] = str_replace(
				'/skin.json',
				'',
				$thing['path']
			);
		}
		if ( !isset( $aExtensions[$sExtName] ) ) {
			throw new BsException( "Unknowen Extension $sExtName" );
		}
		$sExtPath = $aExtensions[$sExtName]['extPath'];
		$sTplDir = implode( '/', [
			$GLOBALS['wgExtensionDirectory'],
			$sExtPath,
			static::$sTemplatePath,
		] );
		$sTplDir = BsFileSystemHelper::normalizePath( $sTplDir );

		$sFullPath = str_replace( $sTplDir, '', $sFullPath );
		$sFullPath = str_replace( '/', '.', $sFullPath );
		$sFullPath = str_replace( static::$sFileExt, '', $sFullPath );
		return "$sExtName$sFullPath";
	}

	/**
	 * DEPRECATED!
	 * Returns HTML for a given template by calling the template function with the given args
	 *
	 * @code
	 *     echo BSTemplateHelper::process(
	 *         'ExampleExtension.Example.Path.ExampleTemplate',
	 *         array(
	 *             'username' => $user->getName(),
	 *             'message' => 'Hello!'
	 *         )
	 *     );
	 * @endcode
	 * @deprecated since version 3.1 - use Services->getService( 'BSTemplateFactory' )->get( $name )
	 * ->process() instead
	 * @param string $sTplName The name of the template
	 * @param mixed $args
	 * @param array $scopes
	 * @param boolean $bForceRecompile
	 * @return string
	 */
	public static function process( $sTplName, array $args = [], array $scopes = [],
		$bForceRecompile = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$template = MediaWikiServices::getInstance()->getService( 'BSTemplateFactory' )
			->get( $sTplName );
		return $template->process( $args, $scopes );
	}

	/**
	 * DEPRECATED!
	 * Returns all Templates and their path in the filesystem
	 * @deprecated since version 3.1 - global templates in RL will be removed
	 * @param array $aReturn
	 * @return array
	 */
	public static function getAllTemplates( $aReturn = [] ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		$aExtensions = $registry->getExtensionDefinitions();
		foreach ( ExtensionRegistry::getInstance()->getAllThings() as $thing ) {
			if ( !isset( $thing['type'] ) || $thing['type'] !== 'skin' ) {
				continue;
			}
			$aExtensions[$thing['name']] = $thing;
		}
		foreach ( $aExtensions as $sExtName => $aConfig ) {
			try {
				$aTplDir = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
					->getTemplateHelper()->makeFullExtTemplatePathFromExtName(
					$sExtName,
					$aConfig
				);
			} catch ( Exception $e ) {
				continue;
			}
			$sPath = implode( '/', $aTplDir );
			if ( !is_dir( $sPath ) ) {
				continue;
			}
			$oRII = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $sPath,
					RecursiveDirectoryIterator::SKIP_DOTS
				),
				RecursiveIteratorIterator::SELF_FIRST
			);
			foreach ( $oRII as $oFile ) {
				if ( $oFile->isDir() ) {
					continue;
				}
				$oFile instanceof SplFileObject;
				$sPath = $oFile->getPathname();
				$sTplName = static::makeTemplateNameFromPath(
					$sExtName,
					$sPath
				);
				if ( isset( $GLOBALS['bsgTemplates'][$sTplName] ) ) {
					$aTplPath = explode( static::$sSeparator, $sTplName );
					$sExtName = array_shift( $aTplPath );

					$sPath = implode( '/', $aTplPath ) . static::$sFileExt;
					$sPath = BsFileSystemHelper::normalizePath( $sPath );
					$sPath = $GLOBALS['bsgTemplates'][$sTplName] . "/" . $sPath;
				}
				$aReturn[ $sTplName ] = $sPath;
			}
		}

		return $aReturn;
	}
}
