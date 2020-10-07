<?php

use BlueSpice\Extension;
use MediaWiki\MediaWikiServices;

/**
 * @deprecated since version 3.0.0 - Use \BlueSpice\Extension instead
 */
abstract class BsExtensionMW extends Extension {

	protected $mExtensionKey = null;
	protected $mExtensionFile = null;
	protected $mExtensionType = null;

	protected $mInfo = null;
	protected $mResourcePath = null;

	protected $sName = '';
	protected $sStatus = '';
	protected $sPackage = '';

	/**
	 *
	 * @var BsCore
	 * @deprecated since version 3.0.0
	 */
	protected $mCore = null;

	protected $aStandardContext = [ '*', '*', '*' ];

	/**
	 * @deprecated since version 3.0.0 - Use constructor instead
	 */
	protected function initExt() {
	}

	/**
	 * Save a reference to current adapter instance.
	 * @deprecated since version 3.0.0
	 * @param BsCore $oCore
	 */
	public function setCore( $oCore ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$this->mCore = $oCore;
	}

	/**
	 * returns the extension informations as an array
	 * @return array
	 */
	public function getInfo() {
		$aExtensions = ExtensionRegistry::getInstance()->getAllThings();
		if ( empty( $aExtensions[$this->sName] ) ) {
			return [
				'status' => $this->sStatus,
				'package' => $this->sPackage,
			];
		}
		return $aExtensions[$this->sName] + [
			'status' => $this->sStatus,
			'package' => $this->sPackage,
		];
	}

	/**
	 * Initializes the extension.
	 * @param string $sExtName
	 * @param array $aConfig
	 * @deprecated since version 3.0.0
	 */
	public function setup( $sExtName = "", $aConfig = [] ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );

		// Extension credits that will show up on Special:Version

		$this->mResourcePath = $GLOBALS['wgScriptPath'] . "/extensions"
			. $aConfig['extPath'] . '/resources';

		$this->sPackage = $aConfig['package'];
		$this->sStatus = $aConfig['status'];
		$this->mExtensionKey = "MW::$sExtName";
		$this->sName = $sExtName;
		$this->initExt();
	}

	/**
	 * register hooks
	 * example: $this->setHook('ParserFirstCallInit'); // register the method
	 * onParserFirstCallInit() to  hook ParserFirstCallInit
	 * example: $this->setHook('ParserFirstCallInit', 'initParser');
	 * // register the method initParser() to hook ParserFirstCallInit
	 * @param string $hook name of the hook to register to
	 * @param string $method (optional) name of method register to
	 * @param bool $bExecuteFirst set this method to be first to be executed when hook triggered
	 * @deprecated since version 3.0.0 - use extension registration instead
	 */
	public function setHook( $hook, $method = false, $bExecuteFirst = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		global $wgHooks;
		// handle $method === 'on'.$hook as if $method == false
		$register = ( $method && ( $method !== 'on' . $hook ) ) ? [ $this, $method ] : $this;
		// do not set same hook twice
		if ( isset( $wgHooks ) && isset( $wgHooks[$hook] )
			&& is_array( $wgHooks[$hook] )
			&& !( count( $wgHooks[$hook] ) && is_object( $wgHooks[$hook][0] )
			&& ( $wgHooks[$hook][0] instanceof Closure ) )
			&& in_array( $register, $wgHooks[$hook], true ) ) {
			return;
		}
		if ( $bExecuteFirst && isset( $wgHooks[$hook] ) ) {
			array_unshift( $wgHooks[$hook], $register );
		} else {
			$wgHooks[$hook][] = & $register;
		}
	}

	/**
	 * Sets the Context
	 * @param \IContextSource $context
	 * @deprecated since version 3.0.0 - This is just for
	 * backwards compatibillity as older extensions may have their own
	 * constructor and therefore do not handover the context to the parent
	 * constructor
	 * @return Extension
	 */
	public function setContext( \IContextSource $context ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$this->context = $context;
		return $this;
	}

	/**
	 * Sets the Config
	 * @param \Config $config
	 * @deprecated since version 3.0.0 - This is just for
	 * backwards compatibillity as older extensions may have their own
	 * constructor and therefore do not handover the config to the parent
	 * constructor
	 * @return Extension
	 */
	public function setConfig( \Config $config ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$this->config = $config;
		return $this;
	}

	/**
	 * Returns the resource path for the current extension
	 * @return string
	 */
	public function getResourcePath() {
		return $this->mResourcePath;
	}

	/**
	 * Returns the name of the extension
	 * @return string
	 */
	public function getName() {
		return $this->sName;
	}

	/**
	 * Returns the key of the extension. 'MW::<name>'
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->mExtensionKey;
	}

	/**
	 *
	 * @return string
	 */
	public function getExtensionPath() {
		return "/{$this->getName()}";
	}

	/**
	 * Returns the image path for the current extension
	 * @param boolean $bResources Whether or not the image directory is located
	 * inside the resources directory
	 * @deprecated since version 3.0.0
	 * @return string
	 */
	public function getImagePath( $bResources = false ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		if ( $bResources ) {
			return $this->getResourcePath() . '/images/';
		}
		return dirname( $this->getResourcePath() ) . '/images/';
	}

	/**
	 * Returns the cache key for this particlular extension
	 * @param string $sSubKey
	 * @deprecated since version 3.0.0
	 * @return string
	 */
	public function getCacheKey( $sSubKey = 'default' ) {
		wfDebugLog( 'bluespice-deprecations', __METHOD__, 'private' );
		$cacheHelper = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
			->getCacheHelper();
		return $cacheHelper->getCacheKey(
			'BlueSpice',
			$this->getName(),
			$sSubKey
		);
	}
}
