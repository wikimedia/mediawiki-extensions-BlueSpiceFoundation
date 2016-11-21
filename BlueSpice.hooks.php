<?php
#$wgHooks['UnitTestsList'][] = 'BsCoreHooks::onUnitTestsList';

// START cache invalidation hooks
$wgHooks['PageContentSaveComplete'][] = 'BsCacheHelper::onPageContentSaveComplete';
// END cache invalidation hooks
