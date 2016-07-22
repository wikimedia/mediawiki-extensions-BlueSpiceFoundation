<?php
class BsCoreHooks {

	protected static $bUserFetchRights = false;

	protected static $loggedInByHash = false;

	public static function onSetupAfterCache() {
		global $wgExtensionFunctions, $wgGroupPermissions, $wgWhitelistRead, $wgMaxUploadSize,
		$wgNamespacePermissionLockdown, $wgSpecialPageLockdown, $wgActionLockdown, $wgNonincludableNamespaces,
		$wgExtraNamespaces, $wgContentNamespaces, $wgNamespacesWithSubpages, $wgNamespacesToBeSearchedDefault,
		$wgLocalisationCacheConf, $wgAutoloadLocalClasses, $wgFlaggedRevsNamespaces, $wgNamespaceAliases, $wgVersion;
		/*
		 * TODO: All those globals above can be removed once all included
		 * settings files use $GLOBALS['wg...'] to access them
		 */

		global $bsgConfigFiles;

		foreach( $bsgConfigFiles as $sConfigFileKey => $sConfigFilePath ) {
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
		$aSoftware['[http://bluespice.com/ ' . $wgBlueSpiceExtInfo['name'] . '] ([' . SpecialPage::getTitleFor( 'SpecialCredits' )->getFullURL() . ' Credits])'] = $wgBlueSpiceExtInfo['version'];
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

		global $wgLogo;
		$wgLogo = BsConfig::get('MW::LogoPath');
	}

	/**
	* Adds the 'ext.bluespice' module to the OutputPage.
	* Adds all Scripts and Styles from BsScriptManager and BsStyleManager
	* Adds ExtJS
	* @param OutputPage $out
	* @param Skin $skin
	* @return boolean
	*/
	public static function onBeforePageDisplay( $out, $skin ) {
		global $IP, $wgFavicon, $wgExtensionAssetsPath;

		// Append CSS for icon font support
		$out->addHeadItem(
			'icomoon-style',
			"\n<link rel=\"stylesheet\" href=\"" .
				htmlspecialchars( $wgExtensionAssetsPath ) .
				"/BlueSpiceFoundation/resources/icomoon/icomoon.icons.css\">\n"
			."<!--[if lt IE 8]>\n<link rel=\"stylesheet\" href=\"" .
				htmlspecialchars( $wgExtensionAssetsPath ) .
				"/BlueSpiceFoundation/resources/icomoon/icomoon.icons.ie7.css\">\n<![endif]-->\n"
			. "<!--[if lt IE 8]>\n<script src=\"" .
				htmlspecialchars( $wgExtensionAssetsPath ) .
				"/BlueSpiceFoundation/resources/icomoon/icomoon.icons.ie7.js\"></script>\n<![endif]-->\n"
		);

		$out->addModules( 'ext.bluespice' );
		$out->addModuleStyles( 'ext.bluespice.styles' );
		$out->addModuleStyles( 'ext.bluespice.compat.vector.styles' );

		$wgFavicon = BsConfig::get( 'MW::FaviconPath' );

		$aScriptBlocks = BsCore::getClientScriptBlocks();
		foreach ( $aScriptBlocks as $sKey => $aClientScriptBlock ) {
			$aOutput[] = '<script type="text/javascript">';
			$aOutput[] = '//'.$aClientScriptBlock[0].' ('.$sKey.')';
			$aOutput[] = $aClientScriptBlock[1];
			$aOutput[] = '</script>';
			$out->addScript( implode( "\n", $aOutput ) );
		}

		//Make some variables available on client side:
		global $wgEnableUploads, $wgMaxUploadSize;
		$iMaxPhpUploadSize = (int) ini_get('upload_max_filesize');
		$aMaxUploadSize = array(
			'php' => 1024*1024*$iMaxPhpUploadSize,
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

		$aExtensionConfs = BsExtensionManager::getRegisteredExtensions();
		$aAssetsPaths = array(
			'BlueSpiceFoundation' => $wgExtensionAssetsPath.'/BlueSpiceFoundation'
		);

		foreach( $aExtensionConfs as $sName => $aConf ) {
			$aAssetsPaths[$sName] = $wgExtensionAssetsPath.$aConf['extPath'];
		}

		//TODO: Implement as RL Module: see ResourceLoaderUserOptionsModule
		$out->addJsConfigVars('bsExtensionManagerAssetsPaths', $aAssetsPaths);
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
	 *
	 * @param array $vars
	 * @param OutputPage $out
	 * @return boolean Always true to keep hook running
	 */
	public static function onMakeGlobalVariablesScript(&$vars, $out) {
		// Necessary otherwise values are not correctly loaded
		$oUser = $out->getUser();

		$aScriptSettings = BsConfig::getScriptSettings();
		wfRunHooks('BsFoundationBeforeMakeGlobalVariablesScript', array( $oUser, &$aScriptSettings ) );

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
		global $wgDBtype;

		$dbw = wfGetDB( DB_MASTER );

		if ( $dbw->tableExists( 'bs_settings' ) ) {
			/* Update routine for incorrect images paths. Some not skin realted images were located
			 * in BlueSpiceSkin and move into Foundation. That an update does not effect any functionality
			 * the following steps were adeded
			 * https://gerrit.wikimedia.org/r/#/c/166979/
			 */
			BsConfig::loadSettings();
			if ( preg_match( '#.*?BlueSpiceSkin.*?bs-logo.png#', BsConfig::get( 'MW::LogoPath' ) ) ) {
				$dbw->delete( 'bs_settings', array( $dbw->addIdentifierQuotes( 'key' ) => 'MW::LogoPath' ) );
			}
			if ( preg_match( '#.*?BlueSpiceSkin.*?favicon.ico#', BsConfig::get( 'MW::FaviconPath' ) ) ) {
				$dbw->delete( 'bs_settings', array( $dbw->addIdentifierQuotes( 'key' ) => 'MW::FaviconPath' ) );
			}
			if ( preg_match( '#.*?BlueSpiceSkin.*?bs-user-default-image.png#', BsConfig::get( 'MW::DefaultUserImage' ) ) ) {
				$dbw->delete( 'bs_settings', array( $dbw->addIdentifierQuotes( 'key' ) => 'MW::DefaultUserImage' ) );
			}
			if ( preg_match( '#.*?BlueSpiceSkin.*?bs-user-anon-image.png#', BsConfig::get( 'MW::AnonUserImage' ) ) ) {
				$dbw->delete( 'bs_settings', array( $dbw->addIdentifierQuotes( 'key' ) => 'MW::AnonUserImage' ) );
			}
			if ( preg_match( '#.*?BlueSpiceSkin.*?bs-user-deleted-image.png#', BsConfig::get( 'MW::DeletedUserImage' ) ) ) {
				$dbw->delete( 'bs_settings', array( $dbw->addIdentifierQuotes( 'key' ) => 'MW::DeletedUserImage' ) );
			}

			return true;
		}

		$table = $dbw->tableName( 'bs_settings' );
		if ( $wgDBtype == 'mysql' || $wgDBtype == 'sqlite') {
			$dbw->query("CREATE TABLE {$table} (`key` varchar(255) NOT NULL, `value` text)");
			$dbw->query("CREATE UNIQUE INDEX `key` ON {$table} (`key`)");
		} elseif ( $wgDBtype == 'postgres' ) {
			$dbw->query("CREATE TABLE {$table} (key varchar(255) NOT NULL, value text)");
			$dbw->query("CREATE UNIQUE INDEX key ON {$table} (key)");
		} elseif ( $wgDBtype == 'oracle' ) {
			$dbw->query("CREATE TABLE {$table} (key VARCHAR2(255) NOT NULL, value LONG NOT NULL)");
			$dbw->query("CREATE UNIQUE INDEX {$dbw->tableName('settings_u01')} ON {$table} (key)");
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
		if (!$module instanceof ApiParse) {
			return true;
		}
		$oTitle = Title::newFromText( $module->getRequest()->getVal( 'page' ) );
		if ( !is_null( $oTitle ) && $oTitle->userCan( 'read' ) == false ) {
			$message = wfMessage('loginreqpagetext', wfMessage('loginreqlink')->plain())->plain();
			return false;
		}
		return true;
	}

	/**
	 * Adds additional data to links generated by the framework. This allows us
	 * to add more functionality to the UI.
	 * @param SkinTemplate $skin
	 * @param Title $target
	 * @param array $options
	 * @param string $html
	 * @param array $attribs
	 * @param string $ret
	 * @return boolean Always true to keep hook running
	 */
	public static function onLinkEnd( $skin, $target, $options, &$html, &$attribs, &$ret ) {
		//We add the original title to a link. This may be the same content as
		//"title" attribute, but it doesn't have to. I.e. in red links
		$attribs['data-bs-title'] = $target->getPrefixedText();

		if( $target->getNamespace() == NS_USER && $target->isSubpage() === false ) {
			//Linker::userLink adds class "mw-userlink" by default
			/*if( !isset($attribs['class']) ) {
				$attribs['class'] = '';
			}
			$attribs['class'] .= ' user';*/
			if( $target->getText() == $html ) {
				$html = htmlspecialchars(
					BsCore::getUserDisplayName(
						User::newFromName($target->getText())
					)
				);
				$attribs['data-bs-username'] = $target->getText();
			}
		}
		return true;
	}


	/**
	 * Adds data attributes to media link tags
	 * THIS IS FOR FUTURE USE: The hook is available starting with MW 1.24!
	 * @param Title $title
	 * @param File $file The File object
	 * @param string $html The content of the resulting  anchor tag
	 * @param array $attribs An array of attributes that will be used in the resulting anchor tag
	 * @param string $ret The HTML output in case the handler returns false
	 * @return boolean Always true to keep hook running
	 */
	public static function onLinkerMakeMediaLinkFile( $title, $file, &$html, &$attribs, &$ret ) {

		$attribs['data-bs-title'] = $title->getPrefixedText();
		if( $file instanceof File ) {
			$attribs['data-bs-filename'] = $file->getName();
		}
		else {
			$attribs['data-bs-filename'] = $title->getText();
		}

		return true;
	}

	/**
	 * @param User $oUser
	 * @param array $aRights
	 * @return boolean
	 */
	public static function onUserGetRights( $oUser, &$aRights ) {
		wfProfileIn('BS::' . __METHOD__);

		if ( $oUser->isAnon() ) {
			$oRequest = RequestContext::getMain()->getRequest();
			$iUserId = $oRequest->getVal( 'u', '' );
			$sUserHash = $oRequest->getVal( 'h', '' );

			if ( !empty( $iUserId ) && !empty( $sUserHash ) ) {
				self::$loggedInByHash = true;
				$_user = User::newFromName( $iUserId );
				if ( $_user !== false && $sUserHash == $_user->getToken() ) {
					$oUser = $_user;
				}
			}
		}

		if ( self::$bUserFetchRights == false ) {
			$aRights = User::getGroupPermissions( $oUser->getEffectiveGroups( true ) );
			# The flag is deactivated to prevent some bugs with the loading of the actual users rights.
			# $this->bUserFetchRights = true;
		}
		wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	/**
	 * This function triggers User::isAllowed when checkPermissionHooks is run
	 * from Title.php. This leads to an early initialization of $user object,
	 * which is needed in order to have correct permission sets in BlueSpice.
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param boolean $result
	 */
	public static function onUserCan( &$title, &$user, $action, &$result ) {
		wfProfileIn('BS::' . __METHOD__);
		if ( !self::$loggedInByHash ) {
			wfProfileIn('--BS::' . __METHOD__ . 'if !$this->loggedInByHash');
			$oRequest = RequestContext::getMain()->getRequest();
			$iUserId = $oRequest->getVal( 'u', '' );
			$sUserHash = $oRequest->getVal( 'h', '' );

			if ( empty( $iUserId ) || empty( $sUserHash ) ) {
				wfProfileOut('--BS::' . __METHOD__ . 'if !self::$loggedInByHash');
				return true;
			}

			$user->mGroups = array();
			$user->getEffectiveGroups( true );
			if ( $iUserId && $sUserHash ) {
				self::$loggedInByHash = true;
				$_user = User::newFromName( $iUserId );
				if ( $_user !== false && $sUserHash == $_user->getToken() ) {
					$result = $_user->isAllowed( 'read' );
					$user = $_user;
				}
			}
			wfProfileOut('--BS::' . __METHOD__ . 'if !self::$loggedInByHash');
		}

		if ( $action == 'read' ) {
			$result = $user->isAllowed( $action );
		}

		wfProfileOut('BS::' . __METHOD__);
		return true;
	}

	/**
	 * Additional chances to reject an uploaded file
	 * @param String $sSaveName: Destination file name
	 * @param String $sTempName: Filesystem path to the temporary file for checks
	 * @param String &$sError: output: message key for message to show if upload canceled by returning false
	 * @return Boolean true on success , false on failure
	 */
	public static function onUploadVerification( $sSaveName, $sTempName, &$sError ) {
		if( empty( $sSaveName ) || !$iFileExt = strrpos( $sSaveName, '.' ) ) {
			return true;
		}

		$sUser = substr( $sSaveName, 0, $iFileExt );
		$oUser = User::newFromName( $sUser );
		if( $oUser instanceof User === false || $oUser->getId() == 0 ) {
			return true;
		}

		$oCurrUser = RequestContext::getMain()->getUser();
		if( $oUser->getId() !== $oCurrUser->getId() ) {
			$sError = 'bs-imageofotheruser';
			return false;
		}

		return true;
	}

	/**
	 * Adds context dependent data to the skintemplate
	 * @param Skin $skin
	 * @param BaseTemplate $template
	 * @return boolean - always true
	 */
	public static function onSkinTemplateOutputPageBeforeExec(&$skin, &$template){

		self::addDownloadTitleAction($skin, $template);
		self::addProfilePageSettings($skin, $template);

		//Compatibility to non-BsBaseTemplate skins
		//This is pretty hacky because Skin-object won't expose Template-object
		//in 'SkinAfterContent' hook (see below)
		if( $template instanceof BsBaseTemplate === false ) {
			self::$oCurrentTemplate = $template; //save for later use
		}

		return true;
	}

	protected static $oCurrentTemplate = null;

	/**
	 * At the moment this is just for compatibility to MediaWiki default
	 * Vector skin. Unfortunately this is too late to add ResourceLoader
	 * modules. Therefore the "ext.bluespice.compat.vector.styles" module get's
	 * always added in "BeforePageDisplay"
	 * @param string $data
	 * @param Skin $skin
	 * @return boolean
	 */
	public static function onSkinAfterContent(  &$data, $skin ) {
		if( self::$oCurrentTemplate == null ) {
			return true;
		}

		if ( isset( self::$oCurrentTemplate->data['bs_dataAfterContent'] ) ) {
			$aData = self::$oCurrentTemplate->data['bs_dataAfterContent'];

			foreach ( $aData as $sExtKey => $aData ) {
				$data .= '<!-- '.$sExtKey.' BEGIN -->';
				$data .= $aData['content'];
				$data .= '<!-- '.$sExtKey.' END -->';
			}
		}

		return true;
	}

	/**
	 * Adds a download action icon to File-pages
	 * @param Skin $skin
	 * @param BaseTemplate $template
	 */
	protected static function addDownloadTitleAction(&$skin, &$template) {
		if( $skin->getTitle()->getNamespace() != NS_FILE ) {
			return;
		}

		$oFile = wfFindFile($skin->getTitle());
		if( $oFile === false ) {
			return;
		}

		if( $oFile->getHandler() instanceof BitmapHandler ) {
			return;
		}

		$template->data['bs_title_actions'][5] = array(
			'id' => 'bs-ta-filedownload',
			'href' => $oFile->getFullUrl(),
			'title' => $oFile->getName(),
			'text' => wfMessage('bs-imagepage-download-text')->plain(),
			'class' => 'icon-download'
		);
	}

	/**
	 * Adds the settings panel on the current user's page
	 * @global string $wgScriptPath
	 * @param Skin $skin
	 * @param BaseTemplate $template
	 */
	protected static function addProfilePageSettings(&$skin, &$template) {
		if ( !$skin->getTitle()->equals( $skin->getUser()->getUserPage() ) ) {
			return; //Run only if on current users profile/userpage
		}

		$oUser = $skin->getUser();
		$oTitle = $skin->getTitle();

		$aSettingViews = array();
		wfRunHooks( 'BS:UserPageSettings', array( $oUser, $oTitle, &$aSettingViews ) );

		$oUserPageSettingsView = new ViewBaseElement();
		$oUserPageSettingsView->setAutoWrap(
			'<div id="bs-userpreferences-settings" class="bs-userpagesettings-item">'.
				'###CONTENT###'.
			'</div>'
		);
		$oUserPageSettingsView->setTemplate(
			'<a href="{URL}" title="{TITLE}">'.
				'<img alt="{IMGALT}" src="{IMGSRC}" />'.
				'<div class="bs-user-label">{TEXT}</div>'.
			'</a>'
		);

		global $wgScriptPath;
		$oUserPageSettingsView->addData(
			array(
				'URL' => htmlspecialchars( Title::newFromText('Special:Preferences')->getLinkURL() ),
				'TITLE' => wfMessage('bs-userpreferences-link-title')->plain(),
				'TEXT' => wfMessage('bs-userpreferences-link-text')->plain(),
				'IMGALT' => wfMessage('bs-userpreferences-link-title')->plain(),
				'IMGSRC' => $wgScriptPath . '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-userpage-settings.png',
			)
		);

		$aSettingViews[] = $oUserPageSettingsView;

		$oProfilePageSettingsView = new ViewBaseElement();
		$oProfilePageSettingsView->setId('bs-userpagesettings');
		$sLabel = wfMessage('bs-userpagesettings-legend')->plain();

		$oProfilePageSettingsFieldsetView = new ViewFormElementFieldset();
		$oProfilePageSettingsFieldsetView->setLabel( $sLabel );

		foreach ( $aSettingViews as $oSettingsView ) {
			$oProfilePageSettingsFieldsetView->addItem($oSettingsView);
		}

		$oProfilePageSettingsView->addItem( $oProfilePageSettingsFieldsetView );
		$template->data['bs_dataAfterContent']['profilepagesettings'] = array(
			'position' => 5,
			'label' => $sLabel,
			'content' => $oProfilePageSettingsView
		);
	}

	/**
	 *
	 * @param Parser $parser
	 * @return boolean Always true to keep hook running
	 */
	public static function onParserFirstCallInit( $parser ) {
		BsGenericTagExtensionHandler::setupHandlers(
			BsExtensionManager::getRunningExtensions(),
			$parser
		);
		return true;
	}

	/**
	 * Register 'bluespice' as extension type
	 * @param array $extTypes
	 */
	public static function onExtensionTypes( &$extTypes ) {
		$extTypes['bluespice'] = wfMessage( "bs-exttype-bluespice" )->plain();
		return true;
	}

	/**
	 * Register PHP Unit Tests with MediaWiki framework
	 * @param array $files
	 * @return boolean Always true to keep hook running
	 */
	public static function onUnitTestsList( &$files ) {
		$oIterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( dirname( __DIR__ ) . '/tests/' )
		);
		/**
		 * @var SplFileInfo $oFileInfo
		 */
		foreach ( $oIterator as $oFileInfo ) {
			if ( substr( $oFileInfo->getFilename(), -8 ) === 'Test.php' ) {
				$files[] = $oFileInfo->getPathname();
			}
		}

		return true;
	}
}
