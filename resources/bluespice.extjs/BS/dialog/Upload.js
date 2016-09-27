Ext.define( 'BS.dialog.Upload', {
	extend: 'BS.Window',
	requires: [
		'BS.panel.Upload'
	],
	minHeight: 50,
	minWidth: 400,
	padding: null,
	title: mw.message( 'bs-upload-uploaddialogtitle' ).plain(),
	layout: 'fit',

	afterInitComponent: function(){
		this.upMain = new BS.panel.Upload({
			id: this.getId()+'-upload-panel',
			allowedFileExtensions: this.allowedFileExtensions
		});
		this.upMain.on ('upload-complete', this.onUpMainUploadComplete, this);

		this.items = [
			this.upMain
		];

		this.callParent(arguments);
	},

	onBtnOKClick: function() {
		this.upMain.uploadFile();
	},

	onUpMainUploadComplete: function( panel, upload ){
		this.fireEvent( 'ok', this, upload );
		this.close();
	},

	resetData: function(){
		this.upMain.getForm().reset();
		this.callParent(arguments);
	}
});