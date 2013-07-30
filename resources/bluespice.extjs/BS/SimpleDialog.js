Ext4.define( 'BS.SimpleDialog', {
	extend: 'Ext4.Window',
	requires: [
		'Ext4.Button'
	],
	width: 300,
	minHeight: 100,
	modal:true,
	buttonAlign: 'center',

	//Custom Settings
	idPrefix: 'overrideMe',

	initComponent: function() {
		if( this.titleMsg ) {
			this.setTitle( mw.message(this.titleMsg).plain() );
		}
		
		this.cntMain = Ext4.create('Ext4.Container',{
			id: this.idPrefix + '-cnt-main',
			padding: 5
		});
		
		this.items = [
			this.cntMain
		];
		
		this.btnOK = Ext4.create('Ext4.Button',{
			text: mw.message('bs-extjs-ok').plain(),
			id: this.idPrefix + '-btn-ok'
		});
		this.btnOK.on( 'click', this.btnOKClicked, this );
		
		this.btnCancel = Ext4.create('Ext4.Button',{
			text: mw.message('bs-extjs-cancel').plain(),
			id: this.idPrefix + '-btn-cancel'
		});
		this.btnCancel.on( 'click', this.btnCancelClicked, this );
		
		this.addEvents( 'ok', 'cancel' );

		this.afterInitComponent(arguments);
		this.callParent( arguments );
	},
	
	afterInitComponent: function () {
		
	},
	
	btnOKClicked: function() {
		this.fireEvent( 'ok' );
		this.close();
	},
	
		
	btnCancelClicked: function() {
		this.fireEvent( 'cancel' );
		this.close();
	}
});