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
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */
use MediaWiki\MediaWikiServices;

/**
 * @package BlueSpiceFoundation
 */
class BSTemplateHelper {
	protected static $sTemplatePath = 'resources/templates';
	protected static $sSeparator = '.';
	protected static $sFileExt = '.mustache';

	protected static function makeFullExtTemplatePathFromExtName( $sExtName ) {
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		$aExtensions = $registry->getExtensionDefinitions();
		if( !isset($aExtensions[$sExtName]) ) {
			throw new BsException( "Unknowen Extension $sExtName" );
		}
		$sExtPath = $aExtensions[$sExtName]['extPath'];
		$aTplDir = array(
			$GLOBALS['wgExtensionDirectory'],
			$sExtPath,
			static::$sTemplatePath
		);
		return $aTplDir;
	}

	protected static function makeTemplateNameFromPath( $sExtName, $sFullPath ) {
		$sFullPath = BsFileSystemHelper::normalizePath( $sFullPath );
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		$aExtensions = $registry->getExtensionDefinitions();
		if( !isset($aExtensions[$sExtName]) ) {
			throw new BsException( "Unknowen Extension $sExtName" );
		}
		$sExtPath = $aExtensions[$sExtName]['extPath'];
		$sTplDir = implode( '/', array(
			$GLOBALS['wgExtensionDirectory'],
			$sExtPath,
			static::$sTemplatePath,
		));
		$sTplDir = BsFileSystemHelper::normalizePath( $sTplDir );

		$sFullPath = str_replace( $sTplDir, '', $sFullPath );
		$sFullPath = str_replace( '/', '.', $sFullPath );
		$sFullPath = str_replace( static::$sFileExt, '', $sFullPath );
		return "$sExtName$sFullPath";
	}

	/**
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
	 * @param string $sTplName The name of the template
	 * @param mixed $args
	 * @param array $scopes
	 * @param boolean $bForceRecompile
	 * @return string
	 */
	public static function process( $sTplName, array $args = [], array $scopes = [], $bForceRecompile = false ) {
		$aTplPath = explode( static::$sSeparator, $sTplName );
		$sTpl = array_pop( $aTplPath );
		$sExtName = array_shift( $aTplPath );
		if( isset($GLOBALS['bsgTemplates'][$sTplName]) ) {
			$aTplDir = explode(
				'/',
				$GLOBALS['bsgTemplates'][$sTplName]
			);
		} else {
			$aTplDir = static::makeFullExtTemplatePathFromExtName( $sExtName );
		}

		$sTemplateDir = implode('/', $aTplPath );
		$sTemplateDir = BsFileSystemHelper::normalizePath( $sTemplateDir );
		$sTemplateDir = implode( '/', $aTplDir ) . "/" . $sTemplateDir;
		$oInstance = new \BlueSpice\TemplateParser( $sTemplateDir, $bForceRecompile );
		return $oInstance->processTemplate( $sTpl, $args, $scopes );
	}

	/**
	 * Returns all Templates and their path in the filesystem
	 * @param array $aReturn
	 * @return array
	 */
	public static function getAllTemplates( $aReturn = [] ) {
		$registry = MediaWikiServices::getInstance()->getService(
			'BSExtensionRegistry'
		);
		$aExtensions = $registry->getExtensionDefinitions();
		foreach( $aExtensions as $sExtName => $aConfig ) {
			try {
				$aTplDir = static::makeFullExtTemplatePathFromExtName(
					$sExtName
				);
			} catch( Exception $e ) {
				continue;
			}
			$sPath = implode('/', $aTplDir);
			if( !is_dir($sPath) ) {
				continue;
			}
			$oRII = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $sPath,
					RecursiveDirectoryIterator::SKIP_DOTS
				),
				RecursiveIteratorIterator::SELF_FIRST
			);
			foreach( $oRII as $oFile ) {
				if( $oFile->isDir() ) {
					continue;
				}
				$oFile instanceof SplFileObject;
				$sPath = $oFile->getPathname();
				$sTplName = static::makeTemplateNameFromPath(
					$sExtName,
					$sPath
				);
				if( isset($GLOBALS['bsgTemplates'][$sTplName]) ) {
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
