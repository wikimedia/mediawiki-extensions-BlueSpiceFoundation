Ext.define( 'BS.form.UserCombo', {
	extend: 'Ext.form.ComboBox',
	requires: [ 'BS.model.User' ],
	displayField: 'display_name',
	valueField: 'user_id',
	labelAlign: 'right',
	forceSelection: true,
	triggerAction: 'all',
	queryMode: 'local',
	typeAhead: true,
	anyMatch: true,
	store: null,
	extraParams: {},

	deferredSetValueConf: false,
	
	initComponent: function() {
		this.setFieldLabel( mw.message('bs-extjs-label-user').plain() );
		if( !this.store ) {
			this.store = Ext.create( 'BS.store.BSApi', {
				apiAction: 'bs-user-store',
				proxy: {
					type: 'ajax',
					url: mw.util.wikiScript( 'api' ),
					reader: {
						type: 'json',
						root: 'results',
						idProperty: 'user_id'
					}
				},
				extraParams: this.extraParams,
				model: 'BS.model.User',
				//autoLoad: true //We need to load manually to have the store
				//loading before rendering. This allows setting values at an early
				//time,
				sorters: [{
					property: this.displayField,
					direction: 'ASC'
				}],
				sortOnLoad: true,
				remoteSort: false
			});
		}
		this.store.load();
		
		this.store.on( 'load', this.onStoreLoad, this );
		
		this.callParent(arguments);
	},
	
	onStoreLoad: function( store, records, successful, eOpts ) {
		if( this.deferredSetValueConf ) {
			this.deferredSetValueConf.callback.apply( 
				this, [this.deferredSetValueConf.value]
			);
			this.deferredSetValueConf = false;
		}
	},
	
	setValueByUserId: function( user_id ) {
		if( this.store.isLoading() ) {
			this.deferSetValue( this.setValueByUserId, user_id );
			return;
		}
		//TODO: store.findRecord()?
		var index = this.store.find( 'user_id', user_id );
		var record = this.store.getAt( index );
		this.setValue(record);
	},
	
	setValueByUserName: function( user_name ) {
		
	},
	
	getUserIdValue: function() {
		
	},
	
	getUserNameValue: function() {
		
	},
	
	getUserModel: function() {
		var user_id = this.getValue();
		return this.store.getById( user_id );
	},
	
	setValue: function( value ) {
		if( this.store.isLoading() ) {
			this.deferSetValue( this.setValue, value );
			return;
		}
		this.callParent( arguments );
	},
	
	deferSetValue: function( callback, value ) {
		this.deferredSetValueConf = {
			callback: callback,
			value: value
		};
	}
});
