Ext4.define( 'BS.Window', {
	extend: 'Ext4.Window',
	requires: [
		'Ext4.Button',
		'Ext4.form.Label'
	],
	width: 400,
	minHeight:250,
	closeAction: 'hide',
	layout: 'form',
	padding:5,
	
	//Custom Setting
	currentData: {},
	
	initComponent: function() {

		this.btnOK = Ext4.create( 'Ext4.Button', {
			text: mw.message('bs-extjs-ok').plain(),
			id: this.getId()+'-btn-ok'
		});
		this.btnOK.on( 'click', this.onBtnOKClick, this );
		
		this.btnCancel = Ext4.create( 'Ext4.Button', {
			text: mw.message('bs-extjs-cancel').plain(),
			id: this.getId()+'-btn-cancel'
		});
		this.btnCancel.on( 'click', this.onBtnCancelClick, this );
		
		this.items = [
			Ext4.create( 'Ext4.form.Label', { text: 'hallo' } )
		]
		
		this.buttons = [
			this.btnOK,
			this.btnCancel
		];

		this.addEvents( 'ok', 'cancel' );
		
		this.afterInitComponent( arguments );

		this.callParent( arguments );
	},
	
	afterInitComponent: function() {
		
	},
	
	onBtnOKClick: function() {
		this.fireEvent( 'ok', this, this.getData() );
		this.hide();
	},
	onBtnCancelClick: function() {
		this.fireEvent( 'cancel', this );
		this.hide();
	},
	
	showLoadMask: function() {
		this.getEl().mask(
			mw.message('bs-extjs-loading').plain()/*,
			'x-mask-loading'*/
		);
	},
	
	hideLoadMask: function() {
		this.getEl().unmask();
	},
	
	getData: function(){
		return this.currentData;
	},
	
	setData: function( obj ){
		this.currentData = obj;
	}/*,
	
	statics: {
		instances: {},
		getInstance: function( key ) {
			
		}
	}*/
});