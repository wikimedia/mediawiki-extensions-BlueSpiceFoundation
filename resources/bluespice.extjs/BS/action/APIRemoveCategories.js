Ext.define( 'BS.action.APIRemoveCategories', {
	extend: 'BS.action.Base',

	categories: [],
	pageTitle: '',

	execute: function(){
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;

		var set = {
			categories: this.categories,
			page_title: this.pageTitle
		};

		this.doAPIRemoveCategories( dfd, set );

		return dfd.promise();
	},

	doAPIRemoveCategories: function( dfd, set ){
		var me = this;

		var taskData = {
			page_title: set.page_title,
			categories: set.categories
		};
		bs.api.tasks.execSilent(
			'wikipage', 'removeCategories', taskData
		)
		.fail(function( response ){
			me.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, set, response );
		})
		.done(function( response ) {
			if( !response.success ){
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, set, response );
			}
			me.actionStatus = BS.action.Base.STATUS_DONE;
			dfd.resolve( me );
		});
	},

	getDescription: function(){
		return mw.message( 'bs-deferred-action-apiremovecategories-description', this.pageTitle ).parse();
	}
});