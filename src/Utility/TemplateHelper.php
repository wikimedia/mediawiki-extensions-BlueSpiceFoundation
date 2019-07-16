<?php
namespace BlueSpice\Utility;

use BlueSpice\Services;

class TemplateHelper {

	const SEPARATOR = '.';
	const TEMPLATE_PATH = 'resources/templates';

	/**
	 *
	 * @var Services
	 */
	protected $services;

	/**
	 * @param Services $services
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
			foreach ( \ExtensionRegistry::getInstance()->getAllThings() as $thing ) {
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

	public function makeFullExtTemplatePathFromExtName( $extName, $config = [] ) {
		if ( isset( $config['type'] ) && $config['type'] === 'skin' ) {
			return [
				$GLOBALS['wgStyleDirectory'],
				$extName,
				static::TEMPLATE_PATH
			];
		}
		$registry = $this->services->getBSExtensionRegistry();
		$extensions = $registry->getExtensionDefinitions();
		if ( !isset( $extensions[$extName] ) ) {
			throw new \BsException( "Unknown Extension $extName" );
		}
		$extPath = $extensions[$extName]['extPath'];
		$tplDirParts = [
			$GLOBALS['wgExtensionDirectory'],
			$extPath,
			static::TEMPLATE_PATH
		];
		return $tplDirParts;
	}
}
