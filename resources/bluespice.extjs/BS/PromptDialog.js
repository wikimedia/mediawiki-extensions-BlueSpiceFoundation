Ext4.define( 'BS.PromptDialog', {
	extend: 'BS.ConfirmDialog',

	afterInitComponent: function() {
		this.callParent( arguments );
		
		this.btnOK.setText( mw.message('bs-extjs-ok').plain() );
		this.btnCancel.setText( mw.message('bs-extjs-cancel').plain() );
		
		this.tfPrompt = Ext4.create( 'Ext4.form.TextField', {
			width: '100%'
		} );
		
		this.cntMain.add(this.tfPrompt);
	},
	
	btnOKClicked: function() {
		var value = this.tfPrompt.getValue();
		this.fireEvent( 'ok', { value: value } );
		this.close();
	}
});