/*
 * Implementation for bs.extensionManager
 */

( function ( mw, bs, $, undefined ) {

	/**
	 * ExtensionManager object
	 *
	 * @param paths
	 */
	function ExtensionManager( paths ) {
		/* Private Members */

		/* Public Members */
		this.paths = paths || new mw.Map();
	}

	bs.extensionManager = new ExtensionManager();
	bs.em = bs.extensionManager;
	bs.em.paths.set( mw.config.get( 'bsExtensionManagerAssetsPaths' ) );

}( mediaWiki, blueSpice, jQuery ) );
