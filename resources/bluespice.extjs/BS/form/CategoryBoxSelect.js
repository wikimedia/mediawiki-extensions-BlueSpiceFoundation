Ext.define( 'BS.form.CategoryBoxSelect', {
	extend:'Ext.ux.form.field.BoxSelect',
	requires: [ 'BS.model.Category' ],
	displayField: 'text',
	valueField: 'text',
	anchor: '95%',
	growMin: 75,
	pinList: true,
	triggerOnClick: true,
	triggerAction: 'all',
	filterPickList: true,
	stacked: true,
	forceSelection: false,
	createNewOnEnter: true,
	queryMode: 'local',
	emptyText: 'Add a category',
	delimiter: ',',
	deferredSetValueConf: false,
	initComponent: function() {
		this.store = Ext.create( 'Ext.data.JsonStore', {
			proxy: {
				type: 'ajax',
				url: bs.util.getCAIUrl( 'getCategoryStoreData' ),
				reader: {
					type: 'json',
					root: 'categories',
					idProperty: 'cat_id'
				}
			},
			model: 'BS.model.Category'
		});
		this.store.load();
		
		this.store.on( 'load', this.onStoreLoad, this );

		this.callParent(arguments);
	},
	onStoreLoad: function( store, records, successful, eOpts ) {
		//this.setValue( "0, 1" );
//		if( this.deferredSetValueConf ) {
//			this.deferredSetValueConf.callback.apply( 
//				this, [this.deferredSetValueConf.value]
//			);
//			this.deferredSetValueConf = false;
//		}
	},
	addValue: function( value ) {
		this.callParent( arguments );
	},
	setValue: function( value ) {
//		if( this.store.isLoading() ) {
//			this.deferSetValue( this.setValue, value );
//			return;
//		}
		this.callParent( arguments );
	},
	setValueByNames: function( names ) {
		this.setValue( names );
		return;
		if( this.store.isLoading() ) {
			this.deferSetValue( this.setValueByNames, names );
			return;
		}
		var indexes = [];
		Ext.each( names, function( name, idx ){
			var index = this.store.find( 'cat_title', name );
			var record = this.store.getAt( index );
			indexes.push( record.get( 'cat_id' ) );
		}, this );

		this.setValue( indexes.join(',') + "" );
		
	},
	deferSetValue: function( callback, value ) {
		this.deferredSetValueConf = {
			callback: callback,
			value: value
		};
	}
});