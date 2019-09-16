Ext.define( 'BS.action.APIAddCategories', {
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

		this.doAPIAddCategories( dfd, set );

		return dfd.promise();
	},

	doAPIAddCategories: function( dfd, set ){
		var me = this;

		var t = mw.Title.newFromText( set.page_title );

		var cnt = {
			wgAction: 'edit',
			wgCanonicalNamespace: t.getNamespacePrefix(),
			wgCanonicalSpecialPageName: false,
			wgNamespaceNumber: t.getNamespaceId(),
			wgPageName: t.getPrefixedText(),
			wgRedirectedFrom: null,
			wgRelevantPageName: t.getPrefixedText(),
			wgTitle:t.getName()
		};

		var taskData = {
			page_title: t.getPrefixedText(),
			categories: set.categories
		};

		bs.api.tasks.exec(
			'wikipage',
			'wikipage-addcategories',
			taskData,
			{
				useService: true,
				context: cnt
			}
		).fail(function( response ){
			me.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, set, response);
		})
			.done(function( response ) {
				if( !response.success ){
					me.actionStatus = BS.action.Base.STATUS_ERROR;
					dfd.reject( me, set, response );
				}
				this.actionStatus = BS.action.Base.STATUS_DONE;
				dfd.resolve( me );
			});
	},

	getDescription: function(){
		return mw.message( 'bs-deferred-action-apiaddcategories-description', this.pageTitle ).parse();
	}
});
