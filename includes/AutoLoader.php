<?php

$wgAutoloadClasses['BsCore'] = __DIR__."/Core.class.php";
$wgAutoloadClasses['BsCoreHooks'] = __DIR__."/CoreHooks.php";

$wgAutoloadClasses['BsPARAM'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsPARAMTYPE'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsPARAMOPTION'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsPATHTYPE'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsRUNLEVEL'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsACTION'] = __DIR__."/Common.php";
$wgAutoloadClasses['BsSTYLEMEDIA'] = __DIR__."/Common.php";
$wgAutoloadClasses['EXTINFO'] = __DIR__."/Common.php";
$wgAutoloadClasses['EXTTYPE'] = __DIR__."/Common.php";

$wgAutoloadClasses['LCStore_BSDB'] = __DIR__."/cache/LCStore_BSDB.php";
$wgAutoloadClasses['LCStore_SHM']  = __DIR__."/cache/LCStore_SHM.php";

$wgAutoloadClasses['BsValidator'] = __DIR__."/validator/Validator.class.php";
$wgAutoloadClasses['BsValidatorPlugin'] = __DIR__."/validator/Validator.class.php";
$wgAutoloadClasses['BsValidatorMwGroupnamePlugin'] = __DIR__."/validator/plugins/BsValidator/BsValidatorMwGroupnamePlugin.class.php";
$wgAutoloadClasses['BsValidatorMwNamespacePlugin'] = __DIR__."/validator/plugins/BsValidator/BsValidatorMwNamespacePlugin.class.php";
$wgAutoloadClasses['BsValidatorMwUsernamePlugin']  = __DIR__."/validator/plugins/BsValidator/BsValidatorMwUsernamePlugin.class.php";

$wgAutoloadClasses['BsSpecialPage'] = __DIR__."/SpecialPage.class.php";
$wgAutoloadClasses['BsConfig'] = __DIR__."/Config.class.php";
$wgAutoloadClasses['BSDebug'] = __DIR__."/Debug.php";
$wgAutoloadClasses['BsException'] = __DIR__."/Exception.class.php";
$wgAutoloadClasses['BsExtensionManager'] = __DIR__."/ExtensionManager.class.php";
$wgAutoloadClasses['BsFileManager'] = __DIR__."/FileManager.class.php";
$wgAutoloadClasses['BsMailer'] = __DIR__."/Mailer.class.php";
$wgAutoloadClasses['BsXHRBaseResponse'] = __DIR__."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsXHRJSONResponse'] = __DIR__."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsXHRResponseStatus'] = __DIR__."/XHRBaseResponse.class.php";
$wgAutoloadClasses['BsCommonAJAXInterface'] = __DIR__."/CommonAJAXInterface.php";
$wgAutoloadClasses['BsCAI'] = __DIR__."/CommonAJAXInterface.php";
$wgAutoloadClasses['BsCAContext'] = __DIR__."/CAContext.php";
$wgAutoloadClasses['BsCAResponse'] = __DIR__."/CAResponse.php";
$wgAutoloadClasses['BsExtJSStoreParams'] = __DIR__."/ExtJSStoreParams.php";

//adapter
$wgAutoloadClasses['BsExtensionMW'] = __DIR__."/ExtensionMW.class.php";
$wgAutoloadClasses['BsInvalidNamespaceException'] = __DIR__."/InvalidNamespaceException.class.php";

//htmlform and htmlformfields
$wgAutoloadClasses['HTMLFormEx'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLInfoFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLTextFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLIntFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLStaticImageFieldOverride'] = __DIR__."/html/htmlformfields/HTMLFormFieldOverrides.php";
$wgAutoloadClasses['HTMLMultiSelectEx'] = __DIR__."/html/htmlformfields/HTMLMultiSelectEx.php";
$wgAutoloadClasses['HTMLMultiSelectPlusAdd'] = __DIR__."/html/htmlformfields/HTMLMultiSelectPlusAdd.php";
$wgAutoloadClasses['HTMLMultiSelectSortList'] = __DIR__."/html/htmlformfields/HTMLMultiSelectSortList.php";

//utility
$wgAutoloadClasses['BsArticleHelper'] = __DIR__."/utility/ArticleHelper.class.php";
$wgAutoloadClasses['BsConnectionHelper'] = __DIR__."/utility/ConnectionHelper.class.php";
$wgAutoloadClasses['BsDOMHelper'] = __DIR__."/utility/DOMHelper.class.php";
$wgAutoloadClasses['BsExtJSHelper'] = __DIR__."/utility/ExtJSHelper.class.php";
$wgAutoloadClasses['BsFormatConverter'] = __DIR__."/utility/FormatConverter.class.php";
$wgAutoloadClasses['BsFileSystemHelper'] = __DIR__."/utility/FileSystemHelper.class.php";
$wgAutoloadClasses['BsGroupHelper'] = __DIR__."/utility/GroupHelper.class.php";
$wgAutoloadClasses['BsLinkProvider'] = __DIR__."/utility/LinkProvider.class.php";
$wgAutoloadClasses['BsNamespaceHelper'] = __DIR__."/utility/NamespaceHelper.class.php";
$wgAutoloadClasses['BsPageContentProvider'] = __DIR__."/utility/PageContentProvider.class.php";
$wgAutoloadClasses['BsStringHelper'] = __DIR__."/utility/StringHelper.class.php";
$wgAutoloadClasses['BsTagFinder'] = __DIR__."/utility/TagFinder.class.php";
$wgAutoloadClasses['BsWidgetListHelper'] = __DIR__."/utility/WidgetListHelper.class.php";

// Outputhandler views
$wgAutoloadClasses['ViewBaseElement'] = __DIR__ . '/outputhandler/views/view.BaseElement.php';
$wgAutoloadClasses['ViewBaseForm'] = __DIR__ . '/outputhandler/views/view.BaseForm.php';
$wgAutoloadClasses['ViewBaseMessage'] = __DIR__ . '/outputhandler/views/view.BaseMessage.php';
$wgAutoloadClasses['ViewEditButton'] = __DIR__ . '/outputhandler/views/view.EditButton.php';
$wgAutoloadClasses['ViewEditButtonPane'] = __DIR__ . '/outputhandler/views/view.EditButtonPane.php';
$wgAutoloadClasses['ViewErrorMessage'] = __DIR__ . '/outputhandler/views/view.ErrorMessage.php';
$wgAutoloadClasses['ViewErrorMessageInline'] = __DIR__ . '/outputhandler/views/view.ErrorMessageInline.php';
$wgAutoloadClasses['ViewException'] = __DIR__ . '/outputhandler/views/view.Exception.php';
$wgAutoloadClasses['ViewFormElement'] = __DIR__ . '/outputhandler/views/view.FormElement.php';
$wgAutoloadClasses['ViewFormElementButton'] = __DIR__ . '/outputhandler/views/view.FormElementButton.php';
$wgAutoloadClasses['ViewFormElementCheckbox'] = __DIR__ . '/outputhandler/views/view.FormElementCheckbox.php';
$wgAutoloadClasses['ViewFormElementCheckboxGroup'] = __DIR__ . '/outputhandler/views/view.FormElementCheckboxGroup.php';
$wgAutoloadClasses['ViewFormElementCommonGroup'] = __DIR__ . '/outputhandler/views/view.FormElementCommonGroup.php';
$wgAutoloadClasses['ViewFormElementFieldset'] = __DIR__ . '/outputhandler/views/view.FormElementFieldset.php';
$wgAutoloadClasses['ViewFormElementInput'] = __DIR__ . '/outputhandler/views/view.FormElementInput.php';
$wgAutoloadClasses['ViewFormElementLabel'] = __DIR__ . '/outputhandler/views/view.FormElementLabel.php';
$wgAutoloadClasses['ViewFormElementRadiobutton'] = __DIR__ . '/outputhandler/views/view.FormElementRadiobutton.php';
$wgAutoloadClasses['ViewFormElementRadioGroup'] = __DIR__ . '/outputhandler/views/view.FormElementRadioGroup.php';
$wgAutoloadClasses['ViewFormElementSelectbox'] = __DIR__ . '/outputhandler/views/view.FormElementSelectbox.php';
$wgAutoloadClasses['ViewFormElementTextarea'] = __DIR__ . '/outputhandler/views/view.FormElementTextarea.php';
$wgAutoloadClasses['ViewNoticeMessage'] = __DIR__ . '/outputhandler/views/view.NoticeMessage.php';
$wgAutoloadClasses['ViewNoticeMessageInline'] = __DIR__ . '/outputhandler/views/view.NoticeMessageInline.php';
$wgAutoloadClasses['ViewTagDefaultMessage'] = __DIR__ . '/outputhandler/views/view.TagDefaultMessage.php';
$wgAutoloadClasses['ViewTagElement'] = __DIR__ . '/outputhandler/views/view.TagElement.php';
$wgAutoloadClasses['ViewTagError'] = __DIR__ . '/outputhandler/views/view.TagError.php';
$wgAutoloadClasses['ViewTagErrorList'] = __DIR__ . '/outputhandler/views/view.TagErrorList.php';
$wgAutoloadClasses['ViewUserBarElement'] = __DIR__ . '/outputhandler/views/view.UserBarElement.php';
$wgAutoloadClasses['ViewUserMiniProfile'] = __DIR__ . '/outputhandler/views/view.UserMiniProfile.php';

//Overrides
// Replace Mediawikis ApiFormatJson class with our own to prevent some errors with the application/json header.
$wgAutoloadClasses['ApiFormatJson'] = __DIR__."/api/ApiFormatJsonMW.php";

/**
 * Behebt einen Bug in Oracle Datenbank-Abstraction der die update.php abstürzen lässt.
 */
$wgAutoloadClasses['DatabaseOracleBase'] = __DIR__."/db/DatabaseOracleBase.php";
if ( version_compare( $wgVersion, '1.19.0', '>' ) ) {
	$wgAutoloadClasses['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePost120.php";
	$wgAutoloadClasses['OracleUpdater']  = __DIR__."/db/OracleUpdater.php";
} else {
	$wgAutoloadClasses['DatabaseOracle'] = __DIR__."/db/DatabaseOraclePre120.php";
}
$wgAutoloadClasses['BSOracleHooks']  = __DIR__."/db/BSOracleHooks.php";

$wgAutoloadClasses['ORAField']  = __DIR__."/db/DatabaseOracleBase.php";
$wgAutoloadClasses['ORAResult'] = __DIR__."/db/DatabaseOracleBase.php";

//Special pages
$wgAutoloadClasses['SpecialDiagnostics'] = __DIR__ . '/specials/SpecialDiagnostics.class.php';
$wgAutoloadClasses['SpecialCredits'] = __DIR__ . '/specials/SpecialCredits.class.php';
