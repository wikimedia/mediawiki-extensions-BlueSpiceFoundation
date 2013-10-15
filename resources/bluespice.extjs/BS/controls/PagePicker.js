//http://stackoverflow.com/questions/6153362/how-to-create-custom-extjs-form-field-component
//http://www.sencha.com/forum/showthread.php?11686-Chained-Combobox-%28Remote%29
Ext.define( "BS.controls.PagePicker", {
	extend: "Ext.form.FieldContainer",
	requires: [
		'BS.model.Namespace',
		'BS.model.Title'
	],
	layout: {
		type: 'hbox'
	},
	//width: 400,
	initComponent: function(){
		this.jstrNamespaces = Ext.create('Ext.data.Store',{
				model: 'BS.model.Namespace',
				proxy: {
					type: 'ajax',
					url: 'app/data/namespaces.json',
					reader: {
						type: 'json',
						root: 'namespaces',
						idProperty: 'namespaceId'
				}
			}
		});
		this.cbNamespaces = Ext.create('Ext.form.ComboBox', {
			store: this.jstrNamespaces,
			displayField: 'namespaceName',
			typeAhead: true,
			flex: 1
		});
		this.cbNamespaces.on( 'select', this.cbNamespacesSelect, this );

		this.jstrTitles = Ext.create('Ext.data.Store',{
			model: 'BS.model.Title',
			proxy: {
				type: 'ajax',
				url: 'app/data/titles.json',
				reader: {
					type: 'json',
					root: 'titles',
					idProperty: 'titlePrefixedText'
				}
			}
		});
		this.cbTitles = Ext.create('Ext.form.ComboBox', {
			store: this.jstrTitles,
			displayField: 'titleText',
			typeAhead: true,
			flex: 2
		});
		this.cbTitles.on( 'select', this.cbTitlesSelect, this );

		this.items = [
			this.cbNamespaces,
			this.cbTitles
		];
		this.callParent( arguments );
		//http://stackoverflow.com/questions/11569284/getting-full-model-object-from-a-combobox-in-extjs
	},

	cbNamespacesSelect: function( combo, records, eOpts ){
		var oNamespace = records[0];
		this.jstrTitles.load({
			params: {
				namespaceId: oNamespace.get('namespaceId')
			}
		});
		console.log( oNamespace );
	},

	cbTitlesSelect: function( combo, records, eOpts ){
		var oTitle = records[0];
		console.log( oTitle );
	},

	getValue: function () {
		return this.cbTitles.findRecordByValue( this.cbTitles.getValue() );
	}
});