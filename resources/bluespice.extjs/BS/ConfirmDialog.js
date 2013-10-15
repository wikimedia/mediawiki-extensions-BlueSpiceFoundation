Ext.define( 'BS.ConfirmDialog', {
	extend: 'BS.AlertDialog',

	afterInitComponent: function() {
		this.callParent( arguments );

		this.btnOK.setText( mw.message('bs-extjs-yes').plain() );
		this.btnCancel.setText( mw.message('bs-extjs-no').plain() );
		this.buttons = [
			this.btnOK,
			this.btnCancel
		];
	}
});