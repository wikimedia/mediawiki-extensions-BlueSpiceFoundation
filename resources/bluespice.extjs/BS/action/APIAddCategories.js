/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 Ext.define('BS.action.ApiAddCategories', {
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

		this.doAPIAddCategories( dfd, set );

		return dfd.promise();
	},

	doAPIAddCategories: function( dfd, set){
		var taskData = {
			page_title: set.page_title,
			categories: set.categories
		};
		bs.api.tasks.execSilent(
			'bs-wikipage-tasks', 'addCategories', taskData
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
		return mw.message('bs-deferred-action-apiaddcategories-description', this.pageTitle).parse();
	}
});


