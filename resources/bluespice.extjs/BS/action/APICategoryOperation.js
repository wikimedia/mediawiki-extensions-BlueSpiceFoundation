Ext.define( 'BS.action.APICategoryOperation', {
	extend: 'BS.action.Base',
	categories: [],
	pageTitle: '',
	pageId: -1,
	task: '',

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

		var taskData = {
			categories: set.categories.join( '|' )
		};

		bs.api.tasks.exec(
			'wikipage',
			this.task,
			taskData,
			{
				useService: true,
				context: this.getContextObject( set )
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

	getContextObject: function( set ){
		var title, context = {
			wgAction: 'edit',
			wgCanonicalSpecialPageName: false,
			wgRedirectedFrom: null
		};
		if ( set.hasOwnProperty( 'page_id' ) ) {
			context.wgArticleId = set.page_id;
			return context;
		}

		if ( set.hasOwnProperty( 'page_title' ) ) {
			title = mw.Title.newFromText( set.page_title );
		}

		context.wgCanonicalNamespace = title.getNamespacePrefix().split( ':' ).shift();
		context.wgPageName = title.getPrefixedText();
		context.wgNamespaceNumber = title.getNamespaceId();
		context.wgRedirectedFrom = title.getPrefixedText();
		context.wgTitle = title.getName();

		return context;
	},

	getDescription: function() {
		// STUB
	}
});


