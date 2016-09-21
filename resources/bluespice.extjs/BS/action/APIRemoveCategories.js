Ext.define('BS.action.ApiRemoveCategories', {
	extend: 'BS.action.Base',

	categories: array(),
	pageTitle: '',

	contructor: function(){
		this.callParent(arguments);
	},

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

	doAPIRemoveCategories: function( dfd, set){
		var taskData = {
			page_title: set.page_title,
			categories: set.categories
		};
		bs.api.tasks.execSilent(
			'bs-wikipage-tasks', 'removeCategories', taskData
		)
		.fail(function( response ){
			this.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( this, set, response);
		})
		.done(function( response ) {
			this.actionStatus = BS.action.Base.STATUS_DONE;
			dfd.resolve(this);
		});
	},

	getDescription: function(){
		return mw.message('bs-deferred-action-apiremovecategories-description', this.pageTitle).parse();
	}
});