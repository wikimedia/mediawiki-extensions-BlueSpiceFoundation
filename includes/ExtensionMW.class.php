<?php

abstract class BsExtensionMW extends ContextSource {

	//<editor-fold desc="Former BsExtension implementation">
	protected $mExtensionFile = null;
	protected $mExtensionType = null;

	protected $mInfo = null;
	protected $mExtensionKey = null;

	protected $mResourcePath = null;

	protected $sName = '';
	protected $sStatus = '';
	protected $sPackage = '';

	/**
	 *
	 * @var BsCore
	 */
	protected $mCore = null;

	protected $aStandardContext = array( '*', '*', '*' );

	protected function initExt() {}

	/**
	 * Save a reference to current adapter instance.
	 * @param BsCore $oCore
	 */
	public function setCore( $oCore ) {
		$this->mCore = $oCore;
	}

	/**
	 * returns the extension informations as an array
	 * @return array
	 */
	public function getInfo() {
		if( !empty( $this->deprecatedSince ) ) {
			return array(
				'path' => $this->mExtensionFile,
				'name' => $this->mInfo[EXTINFO::NAME],
				'version' => $this->mInfo[EXTINFO::VERSION],
				'author' => $this->mInfo[EXTINFO::AUTHOR],
				'url' => $this->mInfo[EXTINFO::URL],
				'descriptionmsg' => $this->mInfo[EXTINFO::DESCRIPTION],
				'status' => $this->sStatus,
				'package' => $this->sPackage,
			);
		}
		$aExtensions = ExtensionRegistry::getInstance()->getAllThings();
		if( empty( $aExtensions[$this->sName] ) ) {
			return array(
				'status' => $this->sStatus,
				'package' => $this->sPackage,
			);
		}
		return $aExtensions[$this->sName] + array(
			'status' => $this->sStatus,
			'package' => $this->sPackage,
		);
	}

	// TODO MRG (01.09.10 01:57): Kommentar
	public function getName() {
		return $this->sName;
	}

	// TODO MRG (01.09.10 01:57): Kommentar
	public function getExtensionKey() {
		return $this->mExtensionKey;
	}

	/**
	 * Initializes the extension.
	 */
	public function setup( $sExtName = "", $aConfig = array() ) {
		wfProfileIn( 'Performance: ' . __METHOD__ );

		global $wgExtensionCredits, $wgBlueSpiceExtInfo;
			// Extension credits that will show up on Special:Version
		if( !empty( $aConfig['deprecatedSince'] ) ) {
			$sVersion = str_replace(
				'default',
				$wgBlueSpiceExtInfo['version'],
				$this->mInfo[EXTINFO::VERSION]
			);

			$wgExtensionCredits[$this->mExtensionType][] = array(
				'path' => $this->mExtensionFile,
				'name' => $this->mInfo[EXTINFO::NAME],
				'version' => $sVersion,
				'author' => $this->mInfo[EXTINFO::AUTHOR],
				'url' => $this->mInfo[EXTINFO::URL],
				'descriptionmsg' => $this->mInfo[EXTINFO::DESCRIPTION]
			);
			$this->deprecatedSince = $aConfig['deprecatedSince'];
		}

		$this->mResourcePath = $GLOBALS['wgScriptPath']."/extensions"
			.$aConfig['extPath'].'/resources';

		$this->sPackage = $aConfig['package'];
		$this->sStatus = $aConfig['status'];
		$this->mExtensionKey = "MW::$sExtName";
		$this->sName = $sExtName;
		$this->initExt();

		wfProfileOut( 'Performance: ' . __METHOD__ );
	}

	/**
	 * register hooks
	 * @example $this->setHook('ParserFirstCallInit'); // register the method onParserFirstCallInit() to  hook ParserFirstCallInit
	 * @example $this->setHook('ParserFirstCallInit', 'initParser'); // register the method initParser() to  hook ParserFirstCallInit
	 * @global array $wgHooks
	 * @param string $hook name of the hook to register to
	 * @param string $method (optional) name of method register to
	 * @param bool $bExecuteFirst set this method to be first to be executed when hook triggered
	 */
	public function setHook( $hook, $method = false, $bExecuteFirst = false ) {
		global $wgHooks;
		// handle $method === 'on'.$hook as if $method == false
		$register = ( $method && ( $method !== 'on' . $hook ) ) ? array( &$this, $method ) : $this;
		// do not set same hook twice
		if ( isset( $wgHooks ) && isset( $wgHooks[$hook] )
			&& is_array( $wgHooks[$hook] )
			&& !(count($wgHooks[$hook]) && is_object($wgHooks[$hook][0])
			&& ($wgHooks[$hook][0] instanceof Closure))
			&& in_array( $register, $wgHooks[$hook], true ) ) {
			return;
		};
		if ( $bExecuteFirst && isset( $wgHooks[$hook] ) ) {
			array_unshift( $wgHooks[$hook], $register );
		} else {
			$wgHooks[$hook][] = & $register;
		}
	}

	/**
	 * Returns the resource path for the current extension
	 * @global string $IP
	 * @global string $wgScriptPath
	 * @return string
	 */
	public function getResourcePath() {
		return $this->mResourcePath;
	}

	/**
	 * Returns the image path for the current extension
	 * @param boolean $bResources Whether or not the image directory is located inside the resources directory
	 * @return string
	 */
	public function getImagePath( $bResources = false ) {
		if ( $bResources ) {
			return $this->getResourcePath().'/images/';
		}
		return dirname( $this->getResourcePath() ).'/images/';
	}

	/**
	 * Returns the cache key for this particlular extension
	 * @param string $sSubKey
	 * @return string
	 */
	public function getCacheKey( $sSubKey = 'default' ) {
		return BsCacheHelper::getCacheKey(
			'BlueSpice',
			$this->getName(),
			$sSubKey
		);
	}

	/**
	 * Returns an array of tag extension definitions
	 * @return array
	 */
	public function makeTagExtensionDefinitions() {
		return array();
	}
}
