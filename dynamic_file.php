<?php
/**
 * This file is the entry point for dynamic file dispatcher.
 */

// So extensions (and other code) can check whether they're running in dynamic
// file mode
define( 'BS_DYNAMIC_FILE', true );

require __DIR__ . '/includes/WebStart.php';

// Set a dummy $wgTitle, because $wgTitle == null breaks various things
// In a perfect world this wouldn't be necessary
$wgTitle = Title::makeTitle(
	NS_SPECIAL,
	'Badtitle/dummy title for dynamic file calls set in dynamic_file.php'
);

// RequestContext will read from $wgTitle, but it will also whine about it.
// In a perfect world this wouldn't be necessary either.
\RequestContext::getMain()->setTitle( $wgTitle );
$fileDispatcher = null;
$dfdFactory = \MediaWiki\MediaWikiServices::getInstance()
	->getService( 'BSDynamicFileDispatcherFactory' );
$request = \RequestContext::getMain()->getRequest();
$dfd = $dfdFactory->newFromParams(
	new \BlueSpice\DynamicFileDispatcher\RequestParams( [], $request ),
	\RequestContext::getMain(),
	true
);

$dfd->getFile()->setHeaders(
	\RequestContext::getMain()->getRequest()->response()
);

$mediawiki = new MediaWiki();
$mediawiki->doPostOutputShutdown( 'fast' );
