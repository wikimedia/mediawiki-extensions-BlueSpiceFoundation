Ext.define( 'BS.PromptDialog', {
	extend: 'BS.ConfirmDialog',

	afterInitComponent: function() {
		this.callParent( arguments );

		this.btnOK.setText( mw.message('bs-extjs-ok').plain() );
		this.btnCancel.setText( mw.message('bs-extjs-cancel').plain() );

		this.tfPrompt = Ext.create( 'Ext.form.TextField', {
			width: '100%'
		} );

		this.cntMain.add(this.tfPrompt);
		this.on( 'show', function() {
			this.tfPrompt.focus();
		}, this );

		this.tfPrompt.on( 'specialkey', function(field, e){
			if ( e.getKey() === e.ENTER ) {
				this.btnOK.fireEvent( 'click', this );
			}
		}, this );
	},

	btnOKClicked: function() {
		var value = this.tfPrompt.getValue();
		this.fireEvent( 'ok', { value: value } );
		this.close();
	}
});