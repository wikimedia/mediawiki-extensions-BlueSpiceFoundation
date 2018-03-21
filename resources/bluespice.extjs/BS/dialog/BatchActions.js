Ext.define( 'BS.dialog.BatchActions', {
	extend: 'MWExt.Dialog',
	requires: [ 'BS.panel.BatchActions' ],

	width: 600,
	autoHeight: true,
	title: mw.message('bs-deferred-batch-title').plain(),
	closable: false,

	makeItems: function() {
		this.pnlBatchActions = new BS.panel.BatchActions();
		this.pnlBatchActions.on( 'processcomplete', this.onProcessComplete, this );

		return [
			this.pnlBatchActions
		];
	},

	makeButtons: function() {
		var buttons = this.callParent( arguments );
		this.btnCancel.hide();
		return buttons;
	},

	setData: function( data ) {
		this.pnlBatchActions.setData( data );
	},

	onBtnOKClick: function() {
		if( this.pnlBatchActions.isProcessComplete() ) {
			this.callParent( arguments );
		}
		else {
			this.startProcessing();
		}
	},

	onProcessComplete: function() {
		this.btnOK.enable();
	},

	startProcessing: function() {
		this.btnOK.disable();
		this.pnlBatchActions.startProcessing();
	}
});