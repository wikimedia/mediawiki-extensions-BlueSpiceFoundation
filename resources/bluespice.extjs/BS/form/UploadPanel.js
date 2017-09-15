Ext.define('BS.form.UploadPanel', {
	extend: 'Ext.form.Panel',
	uploadFormName: 'name',
	uploadFieldLabel: 'FieldsLabel',
	uploadLabelWidth: 50,
	uploadResetButton: true,
	uploadButtonsInline: false,
	initComponent: function() {

		this.ffFile = Ext.create('Ext.form.field.File', {
			xtype: 'filefield',
			name: this.uploadFormName,
			fieldLabel: this.uploadFieldLabel,
			labelWidth: this.uploadLabelWidth,
			msgTarget: 'side',
			allowBlank: false,
			anchor: '100%',
			buttonText: mw.message('bs-extjs-browse').plain()
		});

		this.btnReset = Ext.create("Ext.button.Button", {
			id: "bs-extjs-uploadCombo-" + this.uploadFormName + "-reset-btn",
			text: mw.message('bs-extjs-reset').plain(),
			handler: this.onBtnResetClick,
			flex: 0.5,
			scope: this
		});
		this.btnUpload = Ext.create("Ext.button.Button", {
			id: "bs-extjs-uploadCombo-" + this.uploadFormName + "-upload-btn",
			text: mw.message('bs-extjs-upload').plain(),
			handler: this.onBtnUploadClick,
			flex: 0.5,
			scope: this
		});

		this.items = [
			Ext.create('Ext.form.field.Hidden', {
				name: this.uploadFormName + '-hidden-field',
				id: "bs-extjs-uploadCombo-" + this.uploadFormName + "-hidden-field"
			})
		];
		if (this.uploadButtonsInline) {
			this.ffFile.setMargin('0 5 0 0');

			var fieldContainer = {
				xtype: 'fieldcontainer',
				layout: 'hbox',
				defaults: {
					flex: 1,
					hideLabel: true
				},
				items: [
					this.ffFile,
					this.btnUpload
				]
			};
			if (this.uploadResetButton === true) {
				fieldContainer.items.push(this.btnReset);
				this.btnUpload.setMargin('0 5 0 0');
			}

			this.items.unshift(fieldContainer);
		}
		else {
			this.items.unshift(this.ffFile);

			this.buttons = [];
			if (this.uploadResetButton === true)
				this.buttons.push(this.btnReset);
			this.buttons.push(this.btnUpload);
			this.btnReset.disable();
		}

		this.callParent(arguments);
	},
	onBtnResetClick: function( btn, e ) {
		this.fireEvent('reset', this);
	},
	onBtnUploadClick: function( btn, e ) {
		var form = this.getForm();
		this.fireEvent('upload', this, form);
	}
});