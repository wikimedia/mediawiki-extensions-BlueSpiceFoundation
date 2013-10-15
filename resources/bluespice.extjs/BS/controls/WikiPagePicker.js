//http://stackoverflow.com/questions/6153362/how-to-create-custom-extjs-form-field-component
Ext.define( "BS.WikiPagePicker", {
	extend: "Ext.form.field.Base",
	initComponent: function() {
		this.jstrNamespaces = /*Ext.create('Ext.data.JsonStore',*/ new Ext.data.JsonStore( {
			proxy: {
				type: 'ajax',
				url: wgScript,
				extraParams: {
					'action' : 'ajax',
					'rs': 'BSCommonAJAXInterface::getNamespaces',
					'rsargs[]': Ext.JSON.encode({ contentNamespaces:true })
				},
				reader: {
					type: 'json',
					root: 'namespaces',
					idProperty: 'namespace_id'
				},
				model: 'BS.model.WikiPage',
				autoLoad: true
			}
		});
		this.jstrNamespaces.load();
		//this.jstrPages = Ext.create('Ext.data.JsonStore');
	
		this.cbNamespace = Ext.create( "Ext.form.field.ComboBox", {
			store: this.jstrNamespaces
		});
		this.cbNamespace.on( 'select', this.cbNamespaceSelected, this );
		
		//this.cbPageTitle = Ext.create( "Ext.form.field.ComboBox" );
		this.items = [
			this.cbNamespace//,
			//this.cbPageTitle
		];
		this.callParent( arguments );
	},
	
	cbNamespaceSelected: function(combo, records, eOpts){
		console.log(records);
	}
});