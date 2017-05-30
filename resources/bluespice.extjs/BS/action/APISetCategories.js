Ext.define( 'BS.action.APISetCategories', {
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

		this.doAPISetCategories( dfd, set );

		return dfd.promise();
	},

	doAPISetCategories: function( dfd, set ){
		var me = this;

		var taskData = {
			page_title: set.page_title,
			categories: set.categories
		};
		bs.api.tasks.execSilent(
			'wikipage', 'setCategories', taskData
		)
		.fail(function( response ){
			this.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, set, response );
		})
		.done(function( response ) {
			if( !response.success ){
				this.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, set, response );
			}

			this.actionStatus = BS.action.Base.STATUS_DONE;
			dfd.resolve( me );
		});
	},

	getDescription: function(){
		return mw.message( 'bs-deferred-action-apisetcategories-description', this.pageTitle ).parse();
	}
});