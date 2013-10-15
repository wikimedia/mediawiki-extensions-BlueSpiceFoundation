Ext.define('BS.form.UploadPanel', {
	extend: 'Ext.form.Panel',
	defaultValues: {
		uploadFormName: 'name',
		fieldLabel: 'FieldsLabel',
		labelWidth: 50,
		resetButton: true
	},
	initComponent: function() {
		this.items = [{
				xtype: 'filefield',
				name: this.defaultValues.uploadFormName,
				fieldLabel: this.defaultValues.fieldLabel,
				labelWidth: this.defaultValues.labelWidth,
				msgTarget: 'side',
				allowBlank: false,
				anchor: '100%',
				buttonText: mw.message('bs-extjs-browse').plain()
			}, Ext.create('Ext.form.field.Hidden', {
				name: this.defaultValues.uploadFormName + '-hidden-field',
				id: "bs-extjs-uploadCombo-"+this.defaultValues.uploadFormName+"-hidden-field"
			})];
		this.btnReset = Ext.create("Ext.button.Button", {
			id: "bs-extjs-uploadCombo-"+this.defaultValues.uploadFormName+"-reset-btn",
			text: mw.message('bs-extjs-reset').plain(),
			handler: this.onBtnResetClick,
			scope: this
		});
		this.btnUpload = Ext.create("Ext.button.Button", {
			id: "bs-extjs-uploadCombo-"+this.defaultValues.uploadFormName+"-upload-btn",
			text: mw.message('bs-extjs-upload').plain(),
			handler: this.onBtnUploadClick,
			scope: this
		});
		this.buttons = [];
		if (this.defaultValues.resetButton === true)
			this.buttons.push(this.btnReset);
		this.buttons.push(this.btnUpload);
		this.btnReset.disable();
		this.addEvents('reset', 'upload');
		this.callParent(arguments);
	},
	onBtnResetClick: function() {
		this.fireEvent('reset', this);
	},
	onBtnUploadClick: function(form) {
		var form = this.getForm();
		this.fireEvent('upload', this, form);
	}
});