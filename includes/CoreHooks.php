<?php
class BsCoreHooks {
	
	public static function onSetupAfterCache() {
		global $wgExtensionFunctions, $wgGroupPermissions, $wgWhitelistRead, $wgMaxUploadSize,
		$wgNamespacePermissionLockdown, $wgSpecialPageLockdown, $wgActionLockdown, $wgNonincludableNamespaces,
		$wgExtraNamespaces, $wgContentNamespaces, $wgNamespacesWithSubpages, $wgNamespacesToBeSearchedDefault,
		$wgLocalisationCacheConf, $wgAutoloadLocalClasses, $wgFlaggedRevsNamespaces, $wgNamespaceAliases, $wgVersion;

		$sConfigPath = BSROOTDIR . DS . 'config';
		$aConfigFiles = array( 
			'nm-settings.php', 
			'gm-settings.php', 
			'pm-settings.php' 
		);
		
		foreach( $aConfigFiles as $sConfigFile) {
			$sConfigFilePath = $sConfigPath . DS . $sConfigFile;
			if ( file_exists( $sConfigFilePath ) ) {
				include( $sConfigFilePath );
			}
		}

		return true;
	}

	/**
	 * Called by Special:Version for returning information about the software
	 * @param Array $aSoftware: The array of software in format 'name' => 'version'.
	 */
	public static function onSoftwareInfo( &$aSoftware ) {
		global $wgBlueSpiceExtInfo;
		$aSoftware['[http://www.blue-spice.org/ ' . $wgBlueSpiceExtInfo['name'] . ']'] = $wgBlueSpiceExtInfo['version'];
		return true;
	}
	
	public static function setup() {
		HTMLForm::$typeMappings['staticimage'] = 'HTMLStaticImageFieldOverride';
		HTMLForm::$typeMappings['link'] = 'HTMLInfoFieldOverride';
		HTMLForm::$typeMappings['text'] = 'HTMLTextFieldOverride';
		HTMLForm::$typeMappings['int'] = 'HTMLIntFieldOverride';
		HTMLForm::$typeMappings['multiselectex'] = 'HTMLMultiSelectEx';
		HTMLForm::$typeMappings['multiselectplusadd'] = 'HTMLMultiSelectPlusAdd';
		HTMLForm::$typeMappings['multiselectsort'] = 'HTMLMultiSelectSortList';
	}
	
	/**
	* Adds the 'ext.bluespice' module to the OutputPage.
	* Adds all Scripts and Styles from BsScriptManager and BsStyleManager
	* Adds ExtJS
	* @param OutputPage $out
	* @param Skin $skin
	* @return boolean
	*/
	public static function onBeforePageDisplay( $out, $skin) {
		global $IP,$wgFavicon, $wgExtensionAssetsPath,
			$bsgExtJSFiles, $bsgExtJSThemes, $bsgExtJSTheme;
		
		$out->addModules('ext.bluespice');
		$out->addModules('ext.bluespice.extjs');

		$sSP = BsConfig::get('MW::BlueSpiceScriptPath');
		$wgFavicon = BsConfig::get( 'MW::FaviconPath' );

		//Add ExtJS Files
		self::addNonRLResources($out, $bsgExtJSFiles, 'extjs');
		//Add ExtJS Theme files
		$sTheme = isset($bsgExtJSThemes[$bsgExtJSTheme]) ? $bsgExtJSTheme :'bluespice' ;
		self::addNonRLResources($out, $bsgExtJSThemes[$sTheme], $sTheme);
		
		//Use ExtJS's built-in i18n. This may fail for some languages...
		$sLangCode = preg_replace('/(-|_).*$/', '', $out->getLanguage()->getCode());
		if ($sLangCode != 'en') {
			$out->addScriptFile( $sSP.'/resources/extjs/locale/ext-lang-' . $sLangCode . '.js' ); //ExtJS 4
		}

		$aScriptBlocks = BsCore::getClientScriptBlocks();
		foreach( $aScriptBlocks as $sKey => $aClientScriptBlock ) {
			$aOutput[] = '<script type="text/javascript">';
			$aOutput[] = '//'.$aClientScriptBlock[0].' ('.$sKey.')';
			$aOutput[] = $aClientScriptBlock[1];
			$aOutput[] = '</script>';
			$out->addScript(implode("\n", $aOutput));
		}

		//Make some variables available on client side:
		global $wgEnableUploads, $wgMaxUploadSize;
		$iMaxPhpUploadSize = (int) ini_get('upload_max_filesize');
		$aMaxUploadSize = array(
			'php'       => 1024*1024*$iMaxPhpUploadSize,
			'mediawiki' => $wgMaxUploadSize
		);
		
		//We cannot use BsConfig::RENDER_AS_JAVASCRIPT because we want to 
		//normalize the content to lower case:
		$aFileExtensions  = self::lcNormalizeArray( BsConfig::get( 'MW::FileExtensions' ) );
		$aImageExtensions = self::lcNormalizeArray( BsConfig::get( 'MW::ImageExtensions' ) );
		
		$out->addJsConfigVars( 'bsMaxUploadSize', $aMaxUploadSize );
		$out->addJsConfigVars( 'bsEnableUploads', $wgEnableUploads ); //Was: 'MW::InsertFile::EnableUploads' --> 'bsInsertFileEnableUploads'
		$out->addJsConfigVars( 'bsFileExtensions', $aFileExtensions );
		$out->addJsConfigVars( 'bsImageExtensions', $aImageExtensions );
		$out->addJsConfigVars( 'bsIsWindows', wfIsWindows() );
		
		$aExtensionConfs = BsExtensionManager::getRegisteredExtenions();
		$aAssetsPaths = array(
			'BlueSpiceFoundation' => $wgExtensionAssetsPath.'/BlueSpiceFoundation'
		);
		$aExtJSPaths = array();

		foreach( $aExtensionConfs as $sName => $aConf ) {
			$sAssetsPath = '';
			if( $aConf['baseDir'] == 'ext' ) {
				$sAssetsPath = '/BlueSpiceExtensions/'.$sName;
			} else {
				//TODO: User MWInit::extSetupPath() or similar
				$sAssetsPath = str_replace( $IP.DS.'extensions', '', $aConf['baseDir'] );
				$sAssetsPath = str_replace( "\\", "/", $sAssetsPath );
			}
			$sAssetsPath = $wgExtensionAssetsPath.$sAssetsPath;
			$aAssetsPaths[$sName] = $sAssetsPath;

			//Build (potential) ExtJS Autoloader paths
			$sExtJSNamespace = "BS.$sName";
			$aExtJSPaths[$sExtJSNamespace] = "$sAssetsPath/resources/$sExtJSNamespace";
		}
		//TODO: Implement as RL Module: see ResourceLoaderUserOptionsModule
		$out->addJsConfigVars('bsExtensionManagerAssetsPaths', $aAssetsPaths);
		$sExtJS = 'Ext.BLANK_IMAGE_URL = mw.config.get("wgScriptPath")+"/extensions/BlueSpiceFoundation/resources/bluespice.extjs/images/s.gif";';
		$sExtJS.= 'Ext.Loader.setPath('.FormatJson::encode( $aExtJSPaths).');';
		
		$out->addScript(
			Html::inlineScript( $sExtJS )
		);

		return true;
	}
	
	/**
	 * Performs lower case transformation on every item of an array and removes 
	 * duplicates
	 * @param array $aData One dimensional array of strings
	 * @return array
	 */
	protected static function lcNormalizeArray( $aData ) {
		$aNormalized = array();
		foreach( $aData as $sItem ) {
			$aNormalized[] = strtolower( $sItem );
		}
		return array_unique( $aNormalized );
	}
	
	/**
	 * Adds styles and scripts to document head without using MediaWikis 
	 * resourceloader
	 * HINT: IE 8/9 Issues
	 * http://stackoverflow.com/questions/8092261/extjs-4-load-mask-giving-errors-in-ie8-and-ie9-when-used-while-opening-a-windo
	 * @param OutputPage $out
	 * @param array $aFiles
	 * @param string $sKey Allows HeadItem override
	 */
	protected static function addNonRLResources($out, $aFiles, $sKey) {
		global $wgResourceLoaderDebug, $wgScriptPath;
		
		$aScripts = isset( $aFiles['scripts'] ) ? $aFiles['scripts'] : array();
		$aStyles  = isset( $aFiles['styles'] )  ? $aFiles['styles']  : array();

		if( $out->getRequest()->getVal('debug', 'false') != 'false' 
				|| $wgResourceLoaderDebug ) { //DEBUG Mode
			if( isset($aFiles['debug-scripts']) ) {
				$aScripts = $aFiles['debug-scripts'];
			}
			if( isset($aFiles['debug-styles']) ) {
				$aStyles = $aFiles['debug-styles'];
			}
		}
		$iScriptCount = 0;
		foreach( $aScripts as $sScript ) {
			
			$out->addHeadItem( 
				$sKey.'-'.$iScriptCount, 
				Html::linkedScript( $wgScriptPath.$sScript)
			);
			$iScriptCount++;
		}
		foreach( $aStyles as $sStyle ) {
			$out->addStyle( $wgScriptPath.$sStyle );
		}
	}
	
	/**
	 * 
	 * @param array $vars
	 * @param OutputPage $out
	 * @return boolean Always true to keep hook running
	 */
	public static function onMakeGlobalVariablesScript(&$vars, $out) {
		// Necessary otherwise values are not correctly loaded
		BsConfig::loadSettings();
		BsConfig::loadUserSettings( $out->getUser()->getName() );

		$aScriptSettings = BsConfig::getScriptSettings();

		foreach ( $aScriptSettings as $oVar ) {
			$mValue = $oVar->getValue();
			if( $oVar->getOptions() & BsConfig::TYPE_JSON ) {
				$mValue = json_decode( $mValue );
			}
			// All vars are outputed like this: var bsVisualEditorUse = true
			// VisualEditor = $oVar->getExtension()
			// Use = $oVar->getName()
			// true = $sValue
			$vars['bs'.$oVar->getExtension().$oVar->getName()] = $mValue;
		}

		return true;
	}
	
	/**
	 * 
	 * @param DatabaseUpdater $updater
	 * @return boolean Always true to keep hook running
	 */
	public static function onLoadExtensionSchemaUpdates( $updater ) {
		global $wgDBtype, $wgScriptPath;

		$dbw = wfGetDB( DB_WRITE );
		$table = $dbw->tableName( 'bs_settings' );
		$scriptpath = serialize( "{$wgScriptPath}/extensions/BlueSpiceFoundation" );

		if ( $dbw->tableExists( 'bs_settings' ) ) {
			$res = $dbw->query("SELECT * FROM {$table} WHERE `key` = 'MW::BlueSpiceScriptPath'");
			if ( $dbw->numRows( $res ) < 1 ) {
				$dbw->query("INSERT INTO {$table} (`key`, `value`) VALUES ('MW::BlueSpiceScriptPath', '{$scriptpath}')");
			}
			return true;
		}

		if ( $wgDBtype == 'mysql' ) {
			$dbw->query("DROP TABLE IF EXISTS {$table}");
			$dbw->query("CREATE TABLE {$table} (`key` varchar(255) NOT NULL, `value` text)");
			$dbw->query("CREATE UNIQUE INDEX `key` ON {$table} (`key`)");
			$dbw->query("INSERT INTO {$table} (`key`, `value`) VALUES ('MW::BlueSpiceScriptPath', '{$scriptpath}')");
		} elseif ( $wgDBtype == 'postgres' ) {
			$dbw->query("DROP TABLE IF EXISTS {$table}");
			$dbw->query("CREATE TABLE {$table} (key varchar(255) NOT NULL, value text)");
			$dbw->query("CREATE UNIQUE INDEX key ON {$table} (key)");
			$dbw->query("INSERT INTO {$table} (key, value) VALUES ('MW::BlueSpiceScriptPath', '{$scriptpath}')");
		} elseif ( $wgDBtype == 'oracle' ) {
			$dbw->query("CREATE TABLE {$table} (key VARCHAR2(255) NOT NULL, value LONG NOT NULL)");
			$dbw->query("CREATE UNIQUE INDEX {$dbw->tableName('settings_u01')} ON {$table} (key)");
			$dbw->query("INSERT INTO {$table} (key, value) VALUES ('MW::BlueSpiceScriptPath', '{$scriptpath}')");
		}
		
		return true;
	}
	
	/**
	 * Called during ApiMain::checkCanExecute(), prevents user getting text when lacking permissions
	 * @param ApiBase $module
	 * @param User $user
	 * @param String $message
	 * @return boolean
	 */
	public static function onApiCheckCanExecute( $module, $user, &$message ){
		if (!$module instanceof ApiParse)
			return true;
		if (Title::newFromText($module->getRequest()->getVal('page'))->userCan('read') == false){
			$message = wfMessage('loginreqpagetext', wfMessage('loginreqlink')->plain())->plain();
			return false;
		}
		return true;
	}
}