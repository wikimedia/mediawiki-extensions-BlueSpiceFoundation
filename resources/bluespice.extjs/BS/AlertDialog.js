Ext.define( 'BS.AlertDialog', {
	extend: 'BS.SimpleDialog',

	//Custom Settings
	text: 'setMe',
	textMsg: null,

	afterInitComponent: function() {
		this.callParent( arguments );
		
		var text = this.text;
		if( this.textMsg ) {
			text = mw.message(this.textMsg).plain();
		}
		
		this.cntMain.add({
			html: text,
			border:false,
			margin: '0 0 5px 0'
		});

		this.buttons = [
			this.btnOK
		];
	}
});