Ext.define('BS.action.APIEditPage', {
	extend: 'BS.action.Base',

	//Custom config
	pageTitle: '',
	pageContent: '',

	execute: function() {
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;

		var edit = {
			title: this.pageTitle,
			content: this.pageContent
		};

		this.doAPIEdit( dfd, edit );

		return dfd.promise();
	},

	doAPIEdit: function( dfd, edit ) {
		var me = this;
		this.fireEvent( 'beforesaveedit', this, edit );

		var editPageAPI = new mw.Api();
		editPageAPI.postWithToken( 'edit', {
			'action': 'edit',
			'title': edit.title,
			'text': edit.content,
			'continue': ''
		})
		.fail(function( code, errResp ){
			me.actionStatus = BS.action.Base.STATUS_ERROR;
			dfd.reject( me, edit, errResp );
		})
		.done(function( resp, jqXHR ){
			if( !resp.edit.result || resp.edit.result.toLowerCase() !== 'success' ) {
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, edit, resp );
				return;
			}

			if( resp.edit.title === undefined ) {
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, edit, resp );
				return;
			}

			me.actionStatus = BS.action.Base.STATUS_DONE;
			dfd.resolve( me );
		});
	},

	getDescription: function() {
		return mw.message('bs-deferred-action-apieditpage-description', this.pageTitle).parse();
	}
});