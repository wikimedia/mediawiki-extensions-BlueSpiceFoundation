Ext.define('BS.store.BSApi', {
	extend: 'Ext.data.JsonStore',
	apiAction: null,

	constructor: function( cfg ) {
		cfg = Ext.merge({
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript('api'),
				extraParams: {
					format: 'json',
					context: JSON.stringify( bs.util.getCAIContext() )
				},
				reader: {
					type: 'json',
					rootProperty: 'results',
					idProperty: 'id',
					totalProperty: 'total'
				}
			},
			autoLoad: true,
			remoteSort: true,
			remoteFilter: true
		}, cfg);
		cfg.proxy.extraParams.action = cfg.apiAction || this.apiAction;
		this.callParent([cfg]);
		this.on( 'load', this.checkForEmptyPage, this );
	},

	/**
	 * Checks whether the current page is empty and loads previous one if
	 * possible
	 * @param BS.store.BSApi sender
	 * @param Ext.data.Model[] records
	 * @param Boolean successful
	 * @param Object eOpts
	 * @returns void
	 */
	checkForEmptyPage: function( sender, records, successful, eOpts ) {
		/**
		 * ExtJS Grid/PagingToolbar: a user is on the last page of results
		 * (bigger than "1") and deletes all entries from it.
		 * When the store reloads with the old page info and sees an empty
		 * result set it thinks there are no further results to display, even
		 * if the 'totalProperty' says there are more. It just displays
		 * "0 entries" to the user, leaving him without the change to select
		 * a previous page.
		 * Therefore we check for these circumstances and select a previous
		 * page if possible
		 */
		if( records && records.length === 0 && sender.getTotalCount() !== 0 && sender.currentPage !== 1 ) {
			sender.loadPage( sender.currentPage - 1 );
		}
	}
});