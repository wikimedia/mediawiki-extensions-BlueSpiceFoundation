Ext.define( 'BS.action.APICategoryOperation', {
	extend: 'BS.action.Base',
	categories: [],
	pageTitle: '',
	pageId: -1,
	task: '',

	execute: function () {
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;

		var set = {
			categories: this.categories
		};
		if ( this.pageId !== -1 ) {
			set.page_id = this.pageId;
		} else if ( this.pageTitle !== '' ) {
			set.page_title = this.pageTitle;
		}

		this.doCategoryOperation( dfd, set );

		return dfd.promise();
	},

	doCategoryOperation: function ( dfd, taskData ) {
		var me = this;

		if ( taskData.categories.length > 0 ) {
			taskData.categories = taskData.categories.join( '|' );
		} else {
			delete ( taskData.categories );
		}

		bs.api.tasks.exec(
			'wikipage',
			this.task,
			taskData,
			{
				useService: true,
				context: this.getContextObject( taskData )
			}
		).fail( function ( response ) {
			me.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, taskData, response );
		} )
			.done( function ( response ) {
				if ( !response.success ) {
					me.actionStatus = BS.action.Base.STATUS_ERROR;
					dfd.reject( me, taskData, response );
				}
				me.actionStatus = BS.action.Base.STATUS_DONE;
				dfd.resolve( me );
			} );
	},

	getContextObject: function ( set ) {
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

	getDescription: function () {
		// STUB
	}
} );
