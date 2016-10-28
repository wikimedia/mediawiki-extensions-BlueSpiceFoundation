Ext.define( 'BS.action.APIMovePage', {
	extend: 'BS.action.Base',

	//Custom config
	sourceTitle: '',
	targetTitle: '',
	reason: '',

	execute: function(){
		var me = this;
		var dfd = $.Deferred();
		this.actionStatus = BS.action.Base.STATUS_RUNNING;
		var move = {
			source: me.sourceTitle,
			target: me.targetTitle,
			reason: me.reason
		};

		var movePageAPI = new mw.Api();
			movePageAPI.postWithToken( 'csrf', {
				action: 'move',
				from: this.sourceTitle,
				to: this.targetTitle,
				reason: this.reason,
				movetalk: '',
				noredirect: ''
			})
			.fail(function( move, errResp ){
				me.actionStatus = BS.action.Base.STATUS_ERROR;
				dfd.reject( me, move, errResp );
			})
			.done(function( resp, jqXHR ){
				if( !resp.move.from || !resp.move.to ) {
					me.actionStatus = BS.action.Base.STATUS_ERROR;
					dfd.reject( me, move, resp );
					return;
				}

				me.actionStatus = BS.action.Base.STATUS_DONE;
				dfd.resolve( me );
			});

		return dfd.promise();
	},

	getDescription: function(){
		return mw.message( 'bs-deferred-action-apimovepage-description', this.sourceTitle, this.targetTitle ).parse();
	}
});