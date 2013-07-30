<?php

abstract class BsExtensionMW extends ContextSource {
	
	//<editor-fold desc="Former BsExtension implementation">
	protected $mExtensionFile   = NULL;
	protected $mExtensionType   = NULL;

	protected $mInfo            = NULL;
	protected $mExtensionKey    = NULL;
	/**
	 *
	 * @var BsAdapter
	 */
	public    $mAdapter         = NULL;

	protected $aStandardContext = array('*', '*', '*');

	protected function initExt() {}

	/**
	 * Save a reference to current adapter instance.
	 * @param BsAdapter $adapter
	 */
	public function setAdapter( $adapter ) {
		$this->mAdapter = $adapter;
	}

	/**
	 *
	 * @param string $sExtensionDirectory
	 * @param string $sScriptFileName
	 * @param boolean $bLoadOnDemand
	 * @param boolean $bNeedExtJs
	 * @param boolean $bAvoidLanguageFile
	 */
	public function registerScriptFiles( $sExtensionDirectory, $sScriptFileName, $bLoadOnDemand = true, $bNeedExtJs = false, $bAvoidLanguageFile = false, $sContext = false ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if ( !$sContext ) {
			$sContext = $this->mExtensionKey;
			BsExtensionManager::setContext($this->mExtensionKey);
		}
		if ( $bNeedExtJs ) {
			BsScriptManager::addNeedsExtJs( $sContext );
			//BsCore::loadExtJs();
			//BsExtensionManager::addScriptFileToContext( $sContext, $this->mInfo[EXTINFO::NAME], 'Ext', BsFileManager::ORDER_IF_LOADED );
		}
		$options = ( $bLoadOnDemand ) ? BsFileManager::LOAD_ON_DEMAND : 0;

		BsExtensionManager::addScriptFileToContext( $sContext, $this->mInfo[EXTINFO::NAME], $sExtensionDirectory, $sScriptFileName, $options, !$bAvoidLanguageFile );
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	/**
	 *
	 * @param string $sStyleSheetPath
	 * @param boolean $bLoadOnDemand
	 * @param string $sContext
	 */
	public function registerStyleSheet($sStyleSheetPath, $bLoadOnDemand = true, $sContext = false) {
		wfProfileIn( 'BS::'.__METHOD__ );
		if ( !$sContext ) {
			$sContext = $this->mExtensionKey;
			BsExtensionManager::setContext($this->mExtensionKey);
		}
		$options = ($bLoadOnDemand) ? BsFileManager::LOAD_ON_DEMAND : 0;

		BsExtensionManager::addStyleSheetToContext($sContext, $this->mInfo[EXTINFO::NAME], $sStyleSheetPath, $options);
		wfProfileOut( 'BS::'.__METHOD__ );
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

	// TODO MRG (19.12.10 19:28): Was macht denn das?
	public function runPreferencePlugin( $sAdapterName, $oVariable ) {
		switch($sAdapterName) {
			case 'MW':
			case 'MW115':
			case 'CORE':
				return array();
		}
	}

	// TODO MRG (01.09.10 23:05): Kommentar
	public function registerView( $viewName ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$innerViewFile = str_replace( "View", "", $viewName );
		BsCore::registerClass( $viewName, dirname( $this->mExtensionFile ).DS.'views', 'view.'.$innerViewFile.'.php' );
		wfProfileOut( 'BS::'.__METHOD__ );
	}
	//</editor-fold>

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
		global $wgExtensionCredits;
		// Extension credits that will show up on Special:Version
		$wgExtensionCredits[$this->mExtensionType][] = array(
			'path'        => $this->mExtensionFile,
			'name'        => $this->mInfo[EXTINFO::NAME],
			'version'     => str_replace( "$", "", $this->mInfo[EXTINFO::VERSION] ) . ' (' . $this->mInfo[EXTINFO::STATUS] . ')',
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
	 * returns standard image path for extensions
	 * @return string
	 */
	public function getImagePath( $bResources = false ) {
		// TODO: ScriptPath should be more abstract.
		// TODO: Bluespice-mw should be abstraced
		// CR MRG (30.06.11 10:23): Lokal cachen
		// PW(24.06.2013) added $bResources due to compatibility
		return BsConfig::get( 'MW::ScriptPath' )
				.'/extensions/BlueSpiceExtensions/'
				.$this->mInfo[EXTINFO::NAME]
				.( $bResources ? '/resources' : '' )
				.'/images/';
	}

	/**
	 * If your BlueSpice extension for MediaWiki is in need of a certain
	 * database table just register it's schema file here. Next time update.php
	 * is run from command line the table will automagically be created!
	 * See http://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates for details.
	 * @param string $sSchemaFileName Full path + filename to (.sql) file with create statement
	 */
	protected $aExtensionSchemes = array();
	protected $bExtensionSchemaHookRegistered = false;

	public function registerExtensionSchemaUpdate( $sDatabaseTableName, $sSchemaFileName ) {
		global $wgDBtype;
		// TODO RBV (01.03.11 11:26): Change method to support different update types (i.e. modification)
		$aBaseSettings = BsConfig::get( 'Core::Database' );
		if ( $wgDBtype == 'postgres' ) {
			$sSchemaFileName = substr( $sSchemaFileName, 0, -4 ).'.pg.sql';
		}
		elseif ( $wgDBtype == 'oracle' ) {
			$sSchemaFileName = substr( $sSchemaFileName, 0, -4 ).'.oci.sql';
		}
		$this->aExtensionSchemes[$sDatabaseTableName] = $sSchemaFileName;
		// register mediawiki hook only once
		if ( $this->bExtensionSchemaHookRegistered )
			return;
		global $wgHooks;
		$wgHooks['LoadExtensionSchemaUpdates'][] = array( $this, 'onLoadExtensionSchemaUpdates' );
		$this->bExtensionSchemaHookRegistered = true;
	}

	/**
	 * This method gets called by the MediaWiki Framework
	 * @param DatabaseUpdater $updater Provided by MediaWikis update.php
	 * @return boolean Always true to keep the hook running
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$aNewTables = & BsCore::getInstance( 'MW' )->getAdapter()->ExtNewTables;
		foreach ( $this->aExtensionSchemes as $sDatabaseTableName => $sSchemaFileName ) {
			$aNewTables[] = array( $sDatabaseTableName, $sSchemaFileName );
		}
		return true;
	}
}
