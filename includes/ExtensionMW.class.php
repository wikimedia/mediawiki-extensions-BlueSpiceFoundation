<?php

abstract class BsExtensionMW extends ContextSource {

	//<editor-fold desc="Former BsExtension implementation">
	protected $mExtensionFile   = NULL;
	protected $mExtensionType   = NULL;

	protected $mInfo            = NULL;
	protected $mExtensionKey    = NULL;
	
	protected $mResourcePath    = NULL;

	/**
	 *
	 * @var BsCore 
	 */
	protected $mCore = null;

	protected $aStandardContext = array('*', '*', '*');

	protected function initExt() {}

	/**
	 * Save a reference to current adapter instance.
	 * @param BsAdapter $adapter
	 */
	public function setCore( $oCore ) {
		$this->mCore = $oCore;
	}

	/**
	 * returns the extension informations as an array
	 * @return array
	 */
	public function getInfo() {
		return $this->mInfo;
	}

	// TODO MRG (01.09.10 01:57): Kommentar
	public function getName() {
		return $this->mInfo[EXTINFO::NAME];
	}

	// TODO MRG (01.09.10 01:57): Kommentar
	public function getExtensionKey() {
		return $this->mExtensionKey;
	}

	protected static $aExtensionInstances = array();

	public static function getInstanceFor( $sExtensionKey ) {
		return isset( BsExtensionMW::$aExtensionInstances[$sExtensionKey] ) ? BsExtensionMW::$aExtensionInstances[$sExtensionKey] : false;
		// TODO MRG (29.01.11 23:58): false oder null? als Programmierrichtlinien festlegen
		// TODO RBV (19.05.11 08:49): Ich bin für null. Weil wir ja eigendlich ein Objekt erwarten. Und null ist einem Objekt zumindest ähnlicher als false.
	}

	/**
	 * Initializes the extension.
	 */
	public function setup() {
		global $wgExtensionCredits, $wgBlueSpiceExtInfo;
		// Extension credits that will show up on Special:Version
		$sVersion = str_replace( 'default', $wgBlueSpiceExtInfo['version'], $this->mInfo[EXTINFO::VERSION] );
		$sStatus = str_replace( 'default', $wgBlueSpiceExtInfo['status'], $this->mInfo[EXTINFO::STATUS] );

		$wgExtensionCredits[$this->mExtensionType][] = array(
			'path'        => $this->mExtensionFile,
			'name'        => $this->mInfo[EXTINFO::NAME],
			'version'     => $sVersion . ' (' . $sStatus . ')',
			'author'      => $this->mInfo[EXTINFO::AUTHOR],
			'url'         => $this->mInfo[EXTINFO::URL],
			'description' => $this->mInfo[EXTINFO::DESCRIPTION]
		);
		$this->initExt();
		BsExtensionMW::$aExtensionInstances[$this->mExtensionKey] = &$this;
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
			&& is_array( $wgHooks[$hook] ) && in_array( $register, $wgHooks[$hook] ) )
			return;
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
		if(is_null($this->mResourcePath)) {
			global $IP, $wgScriptPath;
			$sExtensionPath = dirname(str_replace($IP, '', $this->mExtensionFile));
			$sExtensionPath = str_replace( '\\', '/', $sExtensionPath );
			$this->mResourcePath = $wgScriptPath.$sExtensionPath.'/resources';
		}
		return $this->mResourcePath;
	}

	/**
	 * Returns the image path for the current extension
	 * @param boolean $bResources Whether or not the image directory is located inside the resources directory
	 * @return string
	 */
	public function getImagePath( $bResources = false ) {
		if($bResources) {
			return $this->getResourcePath().'/images/';
		}
		return dirname($this->getResourcePath()).'/images/';
	}

}