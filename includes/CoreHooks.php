<?php
class BsCoreHooks {
	
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
		global $wgFavicon, $wgScriptPath, $wgLang, 
				$wgStyleDirectory, $wgResourceLoaderDebug; //TODO: $out->getRequest() for modern MW
		$out->addModules('ext.bluespice');
		$out->addModules('ext.bluespice.extjs');

		$sSP = BsConfig::get('Core::BlueSpiceScriptPath');
		$wgFavicon = BsConfig::get( 'MW::FaviconPath' );

		$out->addStyle( $sSP.'/vendor/ExtJS/resources/css/ext-all-notheme.css' );
		$out->addStyle( $sSP.'/vendor/ExtJS/resources/css/xtheme-gray.css' );
		$out->addStyle( $sSP.'/resources/extjs/resources/ext-theme-classic-sandbox/ext-theme-classic-sandbox-all.css' ); //ExtJS 4: Classic Theme
		//$out->addStyle( $sSP.'/resources/extjs/resources/ext-theme-neptune/ext-theme-neptune-all-sandbox-debug.css' ); //ExtJS 4: Neptune Theme

		if( $out->getRequest()->getVal('debug', 'false') != 'false' || $wgResourceLoaderDebug ) { //DEBUG Mode
			$out->addScriptFile( $sSP.'/vendor/ExtJS/adapter/ext/ext-base-debug.js' );
			$out->addScriptFile( $sSP.'/vendor/ExtJS/ext-all-debug-w-comments.js' );
			$out->addStyle( $sSP.'/vendor/ExtJS/resources/css/debug.css' );

			$out->addScriptFile( $sSP.'/resources/extjs/builds/ext-all-sandbox-debug-w-comments.js' ); //ExtJS 4
			//$out->addScriptFile( $sSP.'/resources/extjs/resources/ext-theme-neptune/ext-theme-neptune-sandbox-debug.js' ); //ExtJS 4: Neptune Theme
		}
		else { 
			$out->addScriptFile( $sSP.'/vendor/ExtJS/adapter/ext/ext-base.js' );
			$out->addScriptFile( $sSP.'/vendor/ExtJS/ext-all.js' );
			
			$out->addScriptFile( $sSP.'/resources/extjs/builds/ext-all-sandbox.js' ); //ExtJS 4
			//$out->addScriptFile( $sSP.'/resources/extjs/resources/ext-theme-neptune/ext-theme-neptune-sandbox.js' ); //ExtJS 4: Neptune Theme
		}

		$sLangCode = preg_replace('/(-|_).*$/', '', $wgLang->getCode());
		if ($sLangCode != 'en') {
			$out->addScriptFile( $sSP.'/vendor/ExtJS/src/locale/ext-lang-' . $sLangCode . '.js' );
			$out->addScriptFile( $sSP.'/resources/extjs/locale/ext-lang-' . $sLangCode . '.js' ); //ExtJS 4
		}
		$out->addScriptFile( $sSP.'/resources/bluespice/BsInitExt.js' );
		$out->addScriptFile( $sSP.'/resources/bluespice/BlueSpiceFramework.js' );


		BsExtensionManager::loadAllScriptFiles();
		$aScriptFiles = BsScriptManager::getFileRegister();
		foreach( $aScriptFiles as $sFile => $iOptions ) {
			$out->addScriptFile($sFile);
		}

		$aScriptBlocks = BsScriptManager::getClientScriptBlocks();
		foreach( $aScriptBlocks as $sKey => $aClientScriptBlock ) {
			$aOutput[] = '<script type="text/javascript">';
			$aOutput[] = '//'.$aClientScriptBlock[0].' ('.$sKey.')';
			$aOutput[] = $aClientScriptBlock[1];
			$aOutput[] = '</script>';
			$out->addScript(implode("\n", $aOutput));
		}

		//TODO: Resolve dependency to "BlueSpiceFrameWork" and move to module "ext.bluespice.skin"
		if( strpos( $wgStyleDirectory, 'bluespice-skin' ) ) {
			$out->addScriptFile( $wgScriptPath.'/bluespice-skin/bluespice/main.js' );
		}


		BsExtensionManager::loadAllStyleSheets();
		$aStyleFiles = BsStyleManager::getFileRegister();
		foreach( $aStyleFiles as $sFile => $iOptions) {
			$out->addStyle($sFile, BsStyleManager::getMediaTypes( $iOptions ) );
		}
		$aInlineStyles = BsStyleManager::getInlineStyles();
		foreach( $aInlineStyles as $media => $styletexts ) {
			$out->addInlineStyle($styletexts);
		}

		return true;
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
		
		echo "Ensure availability of BSCACHEDIR \n";
		BsFileSystemHelper::ensureCacheDirectory();
		
		echo "Ensure availability of BSDATADIR \n";
		BsFileSystemHelper::ensureDataDirectory();
		
		$dbw = wfGetDB( DB_WRITE );
		$table = $dbw->tableName( 'bs_settings' );
		$scriptpath = serialize( "{$wgScriptPath}/extensions/BlueSpiceFoundation" );

		if( $dbw->tableExists( 'bs_settings' ) ) return true;

		if( $wgDBtype == 'mysql' ) {
			$dbw->query("DROP TABLE IF EXISTS {$table}");
			$dbw->query("CREATE TABLE {$table} (`key` varchar(255) NOT NULL, `value` text)");
			$dbw->query("CREATE UNIQUE INDEX `key` ON {$table} (`key`)");
			$dbw->query("INSERT INTO {$table} (`key`, `value`) VALUES ('Core::BlueSpiceScriptPath', '{$scriptpath}')");
		} 
		elseif( $wgDBtype == 'postgres' ) {
			$dbw->query("DROP TABLE IF EXISTS {$table}");
			$dbw->query("CREATE TABLE {$table} (key varchar(255) NOT NULL, value text)");
			$dbw->query("CREATE UNIQUE INDEX key ON {$table} (key)");
			$dbw->query("INSERT INTO {$table} (key, value) VALUES ('Core::BlueSpiceScriptPath', '{$scriptpath}')");
		} 
		elseif( $wgDBtype == 'oracle' ) {
			$dbw->query("CREATE TABLE {$table} (key VARCHAR2(255) NOT NULL, value LONG NOT NULL)");
			$dbw->query("CREATE UNIQUE INDEX {$dbw->tableName('settings_u01')} ON {$table} (key)");
			$dbw->query("INSERT INTO {$table} (key, value) VALUES ('Core::BlueSpiceScriptPath', '{$scriptpath}')");
		}
		
		//Stuff from AdapterMW::onLoadExtensionSchemaUpdates
		//TODO: Still needed?
		$aDBConf = BsConfig::get('Core::Database');
		$sDBName = $aDBConf['core']['dbname'];
		$sDBPrefix = $aDBConf['core']['prefix'];
		$sDBType = $aDBConf['core']['type'];

		$aTables = array(
			//old                       => new
			"{$sDBPrefix}settings" => "{$sDBPrefix}bs_settings",
			"{$sDBPrefix}user_settings" => "{$sDBPrefix}bs_user_settings",
		);

		echo "Updating BlueSpice Core tables...\n";
		$db = BsDatabase::getInstance('CORE');

		foreach ($aTables as $sOldName => $sNewName) {
			if ($sDBType == 'MySQL') {
				$query =
						"SELECT COUNT(*) AS cnt 
			 FROM INFORMATION_SCHEMA.TABLES 
			 WHERE TABLE_SCHEMA = '{$sDBName}' AND TABLE_NAME = '{$sOldName}';";
				$res = $db->query($query);
				$row = $res->fetchObject();
				$res->free();

				if ($row->cnt == 1) {
					echo " Table '{$sOldName}' detected. Renaming to '{$sNewName}' ...";
					$res = $db->query("ALTER TABLE {$sOldName} RENAME TO {$sNewName};");
					$row = $res->fetchObject();
					$res->free();
					echo " done.\n";
				} else {
					echo " Table '{$sOldName}' not found. No renaming needed.\n";
				}
			} else {
				/* try {
					echo " Try to rename table '{$sOldName}' to '{$sNewName}' ...";
					$res = $db->query( "ALTER TABLE {$sOldName} RENAME TO {$sNewName};" );
					$row = $res->fetchObject();
					$res->free();
					echo " done.\n";
					} catch ( Exception $e ) {
					echo " abort.\n Table '{$sOldName}' not found. No renaming needed.\n";
				} */
			}
		}

		return true;
	}
}