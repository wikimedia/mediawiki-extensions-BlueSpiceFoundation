<?php

$GLOBALS['wgAutoloadClasses']['BsCore'] = __DIR__."/Core.class.php";
$GLOBALS['wgAutoloadClasses']['BsCoreHooks'] = __DIR__."/CoreHooks.php";

$GLOBALS['wgAutoloadClasses']['BsPARAM'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPARAMTYPE'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPARAMOPTION'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsPATHTYPE'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsRUNLEVEL'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsACTION'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['BsSTYLEMEDIA'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['EXTINFO'] = __DIR__."/Common.php";
$GLOBALS['wgAutoloadClasses']['EXTTYPE'] = __DIR__."/Common.php";

$GLOBALS['wgAutoloadClasses']['LCStore_BSDB'] = __DIR__."/cache/LCStore_BSDB.php";
$GLOBALS['wgAutoloadClasses']['LCStore_SHM']  = __DIR__."/cache/LCStore_SHM.php";

$GLOBALS['wgAutoloadClasses']['BsValidator'] = __DIR__."/validator/Validator.class.php";
$GLOBALS['wgAutoloadClasses']['BsValidatorPlugin'] = __DIR__."/validator/Validator.class.php";
$GLOBALS['wgAutoloadClasses']['BsValidatorMwGroupnamePlugin'] = __DIR__."/validator/plugins/BsValidator/BsValidatorMwGroupnamePlugin.class.php";
$GLOBALS['wgAutoloadClasses']['BsValidatorMwNamespacePlugin'] = __DIR__."/validator/plugins/BsValidator/BsValidatorMwNamespacePlugin.class.php";
$GLOBALS['wgAutoloadClasses']['BsValidatorMwUsernamePlugin']  = __DIR__."/validator/plugins/BsValidator/BsValidatorMwUsernamePlugin.class.php";

$GLOBALS['wgAutoloadClasses']['BsSpecialPage'] = __DIR__."/SpecialPage.class.php";
$GLOBALS['wgAutoloadClasses']['BsConfig'] = __DIR__."/Config.class.php";
$GLOBALS['wgAutoloadClasses']['BSDebug'] = __DIR__."/Debug.php";
$GLOBALS['wgAutoloadClasses']['BsException'] = __DIR__."/Exception.class.php";
$GLOBALS['wgAutoloadClasses']['BsExtensionManager'] = __DIR__."/ExtensionManager.class.php";
$GLOBALS['wgAutoloadClasses']['BsFileManager'] = __DIR__."/FileManager.class.php";
$GLOBALS['wgAutoloadClasses']['BsMailer'] = __DIR__."/Mailer.class.php";
$GLOBALS['wgAutoloadClasses']['BsXHRBaseResponse'] = __DIR__."/XHRBaseResponse.class.php";
$GLOBALS['wgAutoloadClasses']['BsXHRJSONResponse'] = __DIR__."/XHRBaseResponse.class.php";
$GLOBALS['wgAutoloadClasses']['BsXHRResponseStatus'] = __DIR__."/XHRBaseResponse.class.php";
$GLOBALS['wgAutoloadClasses']['BsCommonAJAXInterface'] = __DIR__."/CommonAJAXInterface.php";
$GLOBALS['wgAutoloadClasses']['BsCAI'] = __DIR__."/CommonAJAXInterface.php";
$GLOBALS['wgAutoloadClasses']['BsCAContext'] = __DIR__."/CAContext.php";
$GLOBALS['wgAutoloadClasses']['BsCAResponse'] = __DIR__."/CAResponse.php";
$GLOBALS['wgAutoloadClasses']['BsExtJSStoreParams'] = __DIR__."/ExtJSStoreParams.php";

//adapter
$GLOBALS['wgAutoloadClasses']['BsExtensionMW'] = __DIR__."/ExtensionMW.class.php";
$GLOBALS['wgAutoloadClasses']['BsInvalidNamespaceException'] = __DIR__."/InvalidNamespaceException.class.php";

//htmlform and htmlformfields
$GLOBALS['wgAutoloadClasses']['HTMLFormEx'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$GLOBALS['wgAutoloadClasses']['HTMLInfoFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$GLOBALS['wgAutoloadClasses']['HTMLTextFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$GLOBALS['wgAutoloadClasses']['HTMLIntFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$GLOBALS['wgAutoloadClasses']['HTMLStaticImageFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$GLOBALS['wgAutoloadClasses']['HTMLMultiSelectEx'] = __DIR__."/html/htmlformfields/HTMLMultiSelectEx.php";
$GLOBALS['wgAutoloadClasses']['HTMLMultiSelectPlusAdd'] = __DIR__."/html/htmlformfields/HTMLMultiSelectPlusAdd.php";
$GLOBALS['wgAutoloadClasses']['HTMLMultiSelectSortList'] = __DIR__."/html/htmlformfields/HTMLMultiSelectSortList.php";

//utility
$GLOBALS['wgAutoloadClasses']['BsArticleHelper'] = __DIR__."/utility/ArticleHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsConnectionHelper'] = __DIR__."/utility/ConnectionHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsDOMHelper'] = __DIR__."/utility/DOMHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsExtJSHelper'] = __DIR__."/utility/ExtJSHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsFormatConverter'] = __DIR__."/utility/FormatConverter.class.php";
$GLOBALS['wgAutoloadClasses']['BsFileSystemHelper'] = __DIR__."/utility/FileSystemHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsGroupHelper'] = __DIR__."/utility/GroupHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsLinkProvider'] = __DIR__."/utility/LinkProvider.class.php";
$GLOBALS['wgAutoloadClasses']['BsNamespaceHelper'] = __DIR__."/utility/NamespaceHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsPageContentProvider'] = __DIR__."/utility/PageContentProvider.class.php";
$GLOBALS['wgAutoloadClasses']['BsStringHelper'] = __DIR__."/utility/StringHelper.class.php";
$GLOBALS['wgAutoloadClasses']['BsTagFinder'] = __DIR__."/utility/TagFinder.class.php";
$GLOBALS['wgAutoloadClasses']['BsWidgetListHelper'] = __DIR__."/utility/WidgetListHelper.class.php";

// Outputhandler views
$GLOBALS['wgAutoloadClasses']['ViewBaseElement'] = __DIR__ . '/outputhandler/views/view.BaseElement.php';
$GLOBALS['wgAutoloadClasses']['ViewBaseForm'] = __DIR__ . '/outputhandler/views/view.BaseForm.php';
$GLOBALS['wgAutoloadClasses']['ViewBaseMessage'] = __DIR__ . '/outputhandler/views/view.BaseMessage.php';
$GLOBALS['wgAutoloadClasses']['ViewEditButton'] = __DIR__ . '/outputhandler/views/view.EditButton.php';
$GLOBALS['wgAutoloadClasses']['ViewEditButtonPane'] = __DIR__ . '/outputhandler/views/view.EditButtonPane.php';
$GLOBALS['wgAutoloadClasses']['ViewErrorMessage'] = __DIR__ . '/outputhandler/views/view.ErrorMessage.php';
$GLOBALS['wgAutoloadClasses']['ViewErrorMessageInline'] = __DIR__ . '/outputhandler/views/view.ErrorMessageInline.php';
$GLOBALS['wgAutoloadClasses']['ViewException'] = __DIR__ . '/outputhandler/views/view.Exception.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElement'] = __DIR__ . '/outputhandler/views/view.FormElement.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementButton'] = __DIR__ . '/outputhandler/views/view.FormElementButton.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementCheckbox'] = __DIR__ . '/outputhandler/views/view.FormElementCheckbox.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementCheckboxGroup'] = __DIR__ . '/outputhandler/views/view.FormElementCheckboxGroup.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementCommonGroup'] = __DIR__ . '/outputhandler/views/view.FormElementCommonGroup.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementFieldset'] = __DIR__ . '/outputhandler/views/view.FormElementFieldset.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementInput'] = __DIR__ . '/outputhandler/views/view.FormElementInput.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementLabel'] = __DIR__ . '/outputhandler/views/view.FormElementLabel.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementRadiobutton'] = __DIR__ . '/outputhandler/views/view.FormElementRadiobutton.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementRadioGroup'] = __DIR__ . '/outputhandler/views/view.FormElementRadioGroup.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementSelectbox'] = __DIR__ . '/outputhandler/views/view.FormElementSelectbox.php';
$GLOBALS['wgAutoloadClasses']['ViewFormElementTextarea'] = __DIR__ . '/outputhandler/views/view.FormElementTextarea.php';
$GLOBALS['wgAutoloadClasses']['ViewNoticeMessage'] = __DIR__ . '/outputhandler/views/view.NoticeMessage.php';
$GLOBALS['wgAutoloadClasses']['ViewNoticeMessageInline'] = __DIR__ . '/outputhandler/views/view.NoticeMessageInline.php';
$GLOBALS['wgAutoloadClasses']['ViewTagDefaultMessage'] = __DIR__ . '/outputhandler/views/view.TagDefaultMessage.php';
$GLOBALS['wgAutoloadClasses']['ViewTagElement'] = __DIR__ . '/outputhandler/views/view.TagElement.php';
$GLOBALS['wgAutoloadClasses']['ViewTagError'] = __DIR__ . '/outputhandler/views/view.TagError.php';
$GLOBALS['wgAutoloadClasses']['ViewTagErrorList'] = __DIR__ . '/outputhandler/views/view.TagErrorList.php';
$GLOBALS['wgAutoloadClasses']['ViewUserBarElement'] = __DIR__ . '/outputhandler/views/view.UserBarElement.php';
$GLOBALS['wgAutoloadClasses']['ViewUserMiniProfile'] = __DIR__ . '/outputhandler/views/view.UserMiniProfile.php';

//Overrides
// Replace Mediawikis ApiFormatJson class with our own to prevent some errors with the application/json header.
$GLOBALS['wgAutoloadClasses']['ApiFormatJson'] = __DIR__."/api/ApiFormatJsonMW.php";

/**
 * Behebt einen Bug in Oracle Datenbank-Abstraction der die update.php abstürzen lässt.
 */
$GLOBALS['wgAutoloadClasses']['DatabaseOracleBase'] = __DIR__."/db/DatabaseOracleBase.php";
if ( version_compare( $wgVersion, '1.19.0', '>' ) ) {
	$GLOBALS['wgAutoloadClasses']['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePost120.php";
	$GLOBALS['wgAutoloadClasses']['OracleUpdater']  = __DIR__."/db/OracleUpdater.php";
} else {
	$GLOBALS['wgAutoloadClasses']['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePre120.php";
}
$GLOBALS['wgAutoloadClasses']['BSOracleHooks']  = __DIR__."/db/BSOracleHooks.php";

$GLOBALS['wgAutoloadClasses']['ORAField']  = __DIR__."/db/DatabaseOracleBase.php";
$GLOBALS['wgAutoloadClasses']['ORAResult'] = __DIR__."/db/DatabaseOracleBase.php";

//Special pages
$GLOBALS['wgAutoloadClasses']['SpecialDiagnostics'] = __DIR__ . '/specials/SpecialDiagnostics.class.php';
$GLOBALS['wgAutoloadClasses']['SpecialCredits'] = __DIR__ . '/specials/SpecialCredits.class.php';
