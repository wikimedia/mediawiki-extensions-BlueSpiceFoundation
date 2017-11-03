Ext.define( 'BS.action.APIDeletePage', {
	extend: 'BS.action.Base',

	//Custom config
	pageTitle: '',

	execute: function () {
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;

		this.doAPIDelete( dfd );

		return dfd.promise();
	},

	doAPIDelete: function ( dfd ) {
		var me = this;

		var deletePageAPI = new mw.Api();
		deletePageAPI.postWithToken( 'csrf', {
			'action': 'delete',
			'title': me.pageTitle
		})
		.fail( function ( code, errResp ) {
			me.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, errResp );
		})
		.done( function ( resp, jqXHR ) {
			if ( resp.delete.title === undefined ) {
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, resp );
				return;
			}

			me.actionStatus = BS.action.Base.STATUS_DONE;
			dfd.resolve( me );
		});
	},

	getDescription: function () {
		return mw.message( 'bs-deferred-action-apideletepage-description', this.pageTitle ).parse();
	}
});