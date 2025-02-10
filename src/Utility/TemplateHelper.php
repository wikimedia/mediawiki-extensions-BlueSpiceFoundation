<?php
namespace BlueSpice\Utility;

use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;

class TemplateHelper {

	public const SEPARATOR = '.';
	public const TEMPLATE_PATH = 'resources/templates';

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $services;

	/**
	 * @param MediaWikiServices $services
	 */
	public function __construct( $services ) {
		$this->services = $services;
	}

	/**
	 *
	 * @param string $tplName
	 * @return string
	 */
	public function getTemplateDirFromName( $tplName ) {
		$pathParts = explode( static::SEPARATOR, $tplName );
		$sTpl = array_pop( $pathParts );
		$extName = array_shift( $pathParts );
		if ( isset( $GLOBALS['bsgTemplates'][$tplName] ) ) {
			$tplDirParts = explode(
				'/',
				$GLOBALS['bsgTemplates'][$tplName]
			);
		} else {
			$config = [];
			foreach ( ExtensionRegistry::getInstance()->getAllThings() as $thing ) {
				if ( !isset( $thing['type'] ) || $thing['type'] !== 'skin' ) {
					continue;
				}
				if ( $thing['name'] !== $extName ) {
					continue;
				}
				$config = $thing;
			}
			$tplDirParts = $this->makeFullExtTemplatePathFromExtName( $extName, $config );
		}

		$tplDir = implode( '/', $pathParts );
		$tplDir = \BsFileSystemHelper::normalizePath( $tplDir );
		$tplDir = implode( '/', $tplDirParts ) . "/" . $tplDir;
		return $tplDir;
	}

	/**
	 *
	 * @param string $extName
	 * @param array $config
	 * @return array
	 * @throws \BsException
	 */
	public function makeFullExtTemplatePathFromExtName( $extName, $config = [] ) {
		if ( isset( $config['type'] ) && $config['type'] === 'skin' ) {
			return [
				$GLOBALS['wgStyleDirectory'],
				$extName,
				static::TEMPLATE_PATH
			];
		}
		$registry = $this->services->getService( 'BSExtensionRegistry' );
		$extensions = $registry->getExtensionDefinitions();
		if ( !isset( $extensions[$extName] ) ) {
			throw new \BsException( "Unknown Extension $extName" );
		}
		$extPath = $extensions[$extName]['extPath'];
		$tplDirParts = [
			$GLOBALS['wgExtensionDirectory'],
			ltrim( $extPath, '/' ),
			static::TEMPLATE_PATH
		];

		return $tplDirParts;
	}
}
