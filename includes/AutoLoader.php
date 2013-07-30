<?php

$sDir = __DIR__;

$wgAutoloadClasses['BsCoreHooks']  = $sDir."/CoreHooks.php";
$wgAutoloadClasses['LCStore_BSDB'] = $sDir."/cache/LCStore_BSDB.php";
$wgAutoloadClasses['LCStore_SHM']  = $sDir."/cache/LCStore_SHM.php";

$wgAutoloadClasses['BsOutputHandler']   = $sDir."/outputhandler/OutputHandler.class.php";
$wgAutoloadClasses['BsValidator']       = $sDir."/validator/Validator.class.php";
$wgAutoloadClasses['BsValidatorPlugin'] = $sDir."/validator/Validator.class.php";

$wgAutoloadClasses['BsAdapter']           = $sDir."/Adapter.class.php";
$wgAutoloadClasses['BsAdapterMW']         = $sDir."/Adapter.class.php";
$wgAutoloadClasses['BsSpecialPage']       = $sDir."/SpecialPage.class.php";
$wgAutoloadClasses['BsConfig']            = $sDir."/Config.class.php";
$wgAutoloadClasses['BsDatabase']          = $sDir."/Database.class.php";
$wgAutoloadClasses['BSDebug']             = $sDir."/Debug.php";
$wgAutoloadClasses['BsException']         = $sDir."/Exception.class.php";
$wgAutoloadClasses['BsExtensionManager']  = $sDir."/ExtensionManager.class.php";
$wgAutoloadClasses['BsFileManager']       = $sDir."/FileManager.class.php";
$wgAutoloadClasses['BsMailer']            = $sDir."/Mailer.class.php";
$wgAutoloadClasses['BsScriptManager']     = $sDir."/ScriptManager.class.php";
$wgAutoloadClasses['BsStyleManager']      = $sDir."/StyleManager.class.php";
$wgAutoloadClasses['BsXHRBaseResponse']   = $sDir."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsXHRJSONResponse']   = $sDir."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsXHRResponseStatus'] = $sDir."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsCommonAJAXInterface'] = $sDir."/CommonAJAXInterface.php";
$wgAutoloadClasses['BsCommonAjaxInterfaceContext'] = $sDir."/CommonAJAXInterface.php";
$wgAutoloadClasses['BsExtJSStoreParams'] = $sDir."/ExtJSStoreParams.php";

//actions
$wgAutoloadClasses['BsRemoteAction'] = $sDir."/actions/RemoteAction.class.php";

//adapter
$wgAutoloadClasses['BsExtensionMW']                 = $sDir."/ExtensionMW.class.php";
$wgAutoloadClasses['BsInvalidNamespaceException']   = $sDir."/InvalidNamespaceException.class.php";
$wgAutoloadClasses['UserPreferencesCompatibleForm'] = $sDir."/UserPreferencesCompatibleForm.php";

//htmlform and htmlformfields
$wgAutoloadClasses['HTMLFormEx']                   = $sDir."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLInfoFieldOverride']        = $sDir."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLTextFieldOverride']        = $sDir."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLIntFieldOverride']         = $sDir."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLStaticImageFieldOverride'] = $sDir."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLMultiSelectEx']            = $sDir."/html/htmlformfields/HTMLMultiSelectEx.php";
$wgAutoloadClasses['HTMLMultiSelectPlusAdd']       = $sDir."/html/htmlformfields/HTMLMultiSelectPlusAdd.php";
$wgAutoloadClasses['HTMLMultiSelectSortList']      = $sDir."/html/htmlformfields/HTMLMultiSelectSortList.php";

//utility
$wgAutoloadClasses['BsArticleHelper']       = $sDir."/utility/ArticleHelper.class.php";
$wgAutoloadClasses['BsConnectionHelper']    = $sDir."/utility/ConnectionHelper.class.php";
$wgAutoloadClasses['BsDOMHelper']           = $sDir."/utility/DOMHelper.class.php";
$wgAutoloadClasses['BsExtJSHelper']         = $sDir."/utility/ExtJSHelper.class.php";
$wgAutoloadClasses['BsFormatConverter']     = $sDir."/utility/FormatConverter.class.php";
$wgAutoloadClasses['BsFileSystemHelper']    = $sDir."/utility/FileSystemHelper.class.php";
$wgAutoloadClasses['BsLinkProvider']        = $sDir."/utility/LinkProvider.class.php";
$wgAutoloadClasses['BsNamespaceHelper']     = $sDir."/utility/NamespaceHelper.class.php";
$wgAutoloadClasses['BsPageContentProvider'] = $sDir."/utility/PageContentProvider.class.php";
$wgAutoloadClasses['BsStringHelper']        = $sDir."/utility/StringHelper.class.php";
$wgAutoloadClasses['BsTagFinder']           = $sDir."/utility/TagFinder.class.php";
$wgAutoloadClasses['BsWidgetListHelper']    = $sDir."/utility/WidgetListHelper.class.php";

// Outputhandler views
$wgAutoloadClasses['ViewBaseElement'] = $sDir . '/outputhandler/views/view.BaseElement.php';
$wgAutoloadClasses['ViewBaseForm'] = $sDir . '/outputhandler/views/view.BaseForm.php';
$wgAutoloadClasses['ViewBaseMessage'] = $sDir . '/outputhandler/views/view.BaseMessage.php';
$wgAutoloadClasses['ViewEditButton'] = $sDir . '/outputhandler/views/view.EditButton.php';
$wgAutoloadClasses['ViewEditButtonPane'] = $sDir . '/outputhandler/views/view.EditButtonPane.php';
$wgAutoloadClasses['ViewErrorMessage'] = $sDir . '/outputhandler/views/view.ErrorMessage.php';
$wgAutoloadClasses['ViewErrorMessageInline'] = $sDir . '/outputhandler/views/view.ErrorMessageInline.php';
$wgAutoloadClasses['ViewException'] = $sDir . '/outputhandler/views/view.Exception.php';
$wgAutoloadClasses['ViewFormElement'] = $sDir . '/outputhandler/views/view.FormElement.php';
$wgAutoloadClasses['ViewFormElementButton'] = $sDir . '/outputhandler/views/view.FormElementButton.php';
$wgAutoloadClasses['ViewFormElementCheckbox'] = $sDir . '/outputhandler/views/view.FormElementCheckbox.php';
$wgAutoloadClasses['ViewFormElementCheckboxGroup'] = $sDir . '/outputhandler/views/view.FormElementCheckboxGroup.php';
$wgAutoloadClasses['ViewFormElementCommonGroup'] = $sDir . '/outputhandler/views/view.FormElementCommonGroup.php';
$wgAutoloadClasses['ViewFormElementFieldset'] = $sDir . '/outputhandler/views/view.FormElementFieldset.php';
$wgAutoloadClasses['ViewFormElementInput'] = $sDir . '/outputhandler/views/view.FormElementInput.php';
$wgAutoloadClasses['ViewFormElementLabel'] = $sDir . '/outputhandler/views/view.FormElementLabel.php';
$wgAutoloadClasses['ViewFormElementRadiobutton'] = $sDir . '/outputhandler/views/view.FormElementRadiobutton.php';
$wgAutoloadClasses['ViewFormElementRadioGroup'] = $sDir . '/outputhandler/views/view.FormElementRadioGroup.php';
$wgAutoloadClasses['ViewFormElementSelectbox'] = $sDir . '/outputhandler/views/view.FormElementSelectbox.php';
$wgAutoloadClasses['ViewFormElementTextarea'] = $sDir . '/outputhandler/views/view.FormElementTextarea.php';
$wgAutoloadClasses['ViewNoticeMessage'] = $sDir . '/outputhandler/views/view.NoticeMessage.php';
$wgAutoloadClasses['ViewNoticeMessageInline'] = $sDir . '/outputhandler/views/view.NoticeMessageInline.php';
$wgAutoloadClasses['ViewTagDefaultMessage'] = $sDir . '/outputhandler/views/view.TagDefaultMessage.php';
$wgAutoloadClasses['ViewTagElement'] = $sDir . '/outputhandler/views/view.TagElement.php';
$wgAutoloadClasses['ViewTagError'] = $sDir . '/outputhandler/views/view.TagError.php';
$wgAutoloadClasses['ViewTagErrorList'] = $sDir . '/outputhandler/views/view.TagErrorList.php';
$wgAutoloadClasses['ViewUserBarElement'] = $sDir . '/outputhandler/views/view.UserBarElement.php';
$wgAutoloadClasses['ViewUserMiniProfile'] = $sDir . '/outputhandler/views/view.UserMiniProfile.php';

//Overrides
// Replace Mediawikis ApiFormatJson class with our own to prevent some errors with the application/json header.
$wgAutoloadClasses['ApiFormatJson'] = $sDir."/api/ApiFormatJsonMW.php";

/**
 * Behebt einen Bug in Oracle Datenbank-Abstraction der die update.php abstürzen lässt.
 */
$wgAutoloadClasses['DatabaseOracleBase'] = $sDir."/db/DatabaseOracleBase.php";
if ($wgVersion > "1.19") {
	$wgAutoloadClasses['DatabaseOracle'] = $sDir."/db/DatabaseOraclePost120.php";
	$wgAutoloadClasses['OracleUpdater']  = $sDir."/db/OracleUpdater.php";
} else {
	$wgAutoloadClasses['DatabaseOracle'] = $sDir."/db/DatabaseOraclePre120.php";
}
$wgAutoloadClasses['BSOracleHooks']  = $sDir."/db/BSOracleHooks.php";

//TODO: WHERE ARE THOSE FILES?
$wgAutoloadClasses['ORAField']  = $sDir."/db/DatabaseOracle.php";
$wgAutoloadClasses['ORAResult'] = $sDir."/db/DatabaseOracle.php";

//diagnostics special page
$wgAutoloadClasses['SpecialDiagnostics'] = $sDir . '/specials/SpecialDiagnostics.class.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgSpecialPageGroups['Diagnostics'] = 'bluespice';
$wgExtensionMessagesFiles['DiagnosticsAlias'] = $sDir . '../languages/BlueSpice.Diagnostics.alias.php'; # Location of an aliases file (Tell MediaWiki to load this file)
$wgSpecialPages['Diagnostics'] = 'SpecialDiagnostics'; # Tell MediaWiki about the new special page and its class name
