<?php
// START cache invalidation hooks
$wgHooks['PageContentSaveComplete'][] = 'BsCacheHelper::onPageContentSaveComplete';
// END cache invalidation hooks

if ( !isset( $wgHooks['EditPage::showEditForm:initial'] ) ) {
	$wgHooks['EditPage::showEditForm:initial'] = array();
}

if ( $wgDBtype == 'oracle' ) {
	$wgHooks['ArticleDelete'][] = 'BSOracleHooks::onArticleDelete';
}
