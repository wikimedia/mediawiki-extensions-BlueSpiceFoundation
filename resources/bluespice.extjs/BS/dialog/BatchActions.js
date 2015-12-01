Ext.define( 'BS.dialog.BatchActions', {
	extend: 'BS.Window',
	requires: [ 'BS.panel.BatchActions' ],

	width: 600,
	autoHeight: true,
	title: mw.message('bs-deferred-batch-title').plain(),
	closable: false,

	afterInitComponent: function() {
		this.pnlBatchActions = new BS.panel.BatchActions();
		this.pnlBatchActions.on( 'processcomplete', this.onProcessComplete, this );

		this.items = [
			this.pnlBatchActions
		];

		this.btnCancel.hide();

		this.callParent( arguments );
	},

	setData: function( data ) {
		this.pnlBatchActions.setData( data );
		this.callParent( arguments );
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