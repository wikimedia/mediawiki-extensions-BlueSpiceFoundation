Ext.define('BS.action.APICopyPage', {
	extend: 'BS.action.APIEditPage',

	//Custom Settings
	sourceTitle: '',
	targetTitle: '',

	execute: function() {
		var me = this;
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;
		var copy = {
			sourceTitle: me.sourceTitle,
			targetTitle: me.targetTitle
		};

		var getCurrentTextAPI = new mw.Api();
			getCurrentTextAPI.get({
				action: 'query',
				titles: this.sourceTitle,
				prop: 'revisions',
				rvprop: 'content',
				indexpageids : ''
			})
			.fail(function( code, errResp ){
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, copy, errResp );
			})
			.done(function( resp, jqXHR ){
				var pageId = resp.query.pageids[0];
				var pageInfo = resp.query.pages[pageId];
				if( pageInfo.missing || !pageInfo.revisions || !pageInfo.revisions[0] ) {
					me.actionStatus = BS.action.Base.STATUS_ERROR;
					dfd.reject( me, copy, resp );
					return;
				}
				me.pageTitle = me.targetTitle;
				me.pageContent = pageInfo.revisions[0]['*'];
				var basePromise = me.superclass.execute.apply( me );
				basePromise.fail(function() {
					dfd.reject.apply( dfd, arguments );
				}).done( function() {
					dfd.resolve.apply( dfd, arguments );
				});
			});

		return dfd.promise();
	},

	getDescription: function() {
		return mw.message('bs-deferred-action-apicopypage-description', this.sourceTitle, this.targetTitle).parse();
	}
});