Ext.define( 'BS.SimpleDialog', {
	extend: 'Ext.Window',
	requires: [
		'Ext.Button'
	],
	width: 300,
	minHeight: 100,
	modal:true,
	closeAction: 'destroy',
	buttonAlign: 'center',

	//Custom Settings
	idPrefix: 'overrideMe',

	initComponent: function() {
		if( this.titleMsg ) {
			this.setTitle( mw.message(this.titleMsg).plain() );
		}

		this.cntMain = Ext.create('Ext.Container',{
			id: this.idPrefix + '-cnt-main',
			padding: 5
		});

		this.items = [
			this.cntMain
		];

		this.btnOK = Ext.create('Ext.Button',{
			text: mw.message('bs-extjs-ok').plain(),
			id: this.idPrefix + '-btn-ok'
		});
		this.btnOK.on( 'click', this.btnOKClicked, this );

		this.btnCancel = Ext.create('Ext.Button',{
			text: mw.message('bs-extjs-cancel').plain(),
			id: this.idPrefix + '-btn-cancel'
		});
		this.btnCancel.on( 'click', this.btnCancelClicked, this );


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