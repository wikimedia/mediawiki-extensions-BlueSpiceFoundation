Ext.define('BS.form.field.TitleCombo', {
	extend: 'MWExt.form.field.GridPicker',
	requires:[ 'BS.model.Title', 'BS.store.BSApi' ],

	//This is necessary to make the ComboBox return a Model
	//instance if input is less then 4 chars
	minChars: 1,
	triggerAction: 'last',
	emptyText: mw.message( "bs-extjs-combo-box-default-placeholder" ).plain(),

	gridConfig: {
		border:true,
		hideHeaders: true,
		features: [{
			ftype: 'grouping',
			groupHeaderTpl: [
				'{name:this.formatName}',
				{
					formatName: function(name) {
						if( name === 'namespace' ) {
							return mw.message('bs-extjs-label-namespace').plain();
						}
						if( name === 'wikipage' || name === 'specialpage' ) {
							return mw.message('bs-extjs-label-page').plain();
						}
						if( name === 'directsearch' ) {
							return mw.message('bs-extjs-label-directsearch').plain();
						}
						return name;
					}
				}
			],
			collapsible: false
		}],
		columns: [{
			dataIndex: 'displayText',
			renderer: function( value, meta, record ) {
				if( record.get( 'type' ) === 'namespace' ) {
					return value;
				}
				if( record.get( 'type' ) === 'specialpage' ) {
					return value;
				}
				if( record.get( 'page_id' ) === 0 ) {
					return value + ' <sup><span class="new-page-hint">' + mw.message( 'bs-extjs-titlecombo-newpagehint' ).plain() + '</span></sup>';
				}
				return value;
			},
			flex: 1
		}],
		dockedItems: [{
			xtype: 'pagingtoolbar',
			store: this.store,
			dock: 'bottom',
			displayInfo: true
		}],
		viewConfig: {
			getRowClass: function(record, rowIndex, rowParams, store){
				var cssClass = 'bs-model-title-type-namespace';
				if( record.get( 'type' ) === 'namespace' ) {
					return cssClass;
				}
				cssClass = 'bs-model-title-type-specialpage';
				if( record.get( 'type' ) === 'specialpage' ) {
					return cssClass;
				}
				cssClass = 'bs-model-title-type-title';
				if( record.get( 'page_id' ) === 0 ) {
					cssClass += ' new';
				}
				return cssClass;
			}
		}
	},

	excludeIds: false,

	constructor: function( conf ) {
		//May not be overridden
		conf.queryMode = 'remote';
		conf.displayField = 'displayText';
		conf.valueField = 'prefixedText';
		conf.typeAhead = true;
		conf.forceSelection = true;

		this.callParent([conf]);
	},

	initComponent: function() {
		this.store = this.makeStore();

		this.callParent( arguments );
	},

	makeStore: function() {
		var namespaces = mw.config.get( 'bsgTitleComboDefaultNamespaces', [] );
		if( this.excludeIds !== false  ) {
			namespaces = bs.ns.filter.allBut( this.excludeIds );
		}
		var options = {
			returnQuery: true,
			namespaces : namespaces
		};

		var store = new BS.store.BSApi({
			apiAction: 'bs-titlequery-store',
			proxy: {
				extraParams: {
					options: Ext.encode( options )
				}
			},
			model: 'BS.model.Title',
			groupField: 'type',
			remoteSort: true,
			autoLoad: true
		});

		store.on( "beforeload", function( store, operation, eOpts ){
			operation.params = operation.params || {};
			operation.params.query = this.lastQuery;
		}, this );

		return store;
	},

	getValue: function() {
		var value = this.callParent(arguments);

		if( ( value instanceof BS.model.Title ) === false ) {
			value = this.findRecordByValue(value);
		}

		return value;
	},

	setValue: function( value, doSelect, skipLoad ) {
		var me = this;

		if( !value || value === '') {
			return me.callParent([value, doSelect]);
		}

		if( Ext.isArray(value) ) {
			value = value[0];
		}

		var textValue = value;
		if( value instanceof BS.model.Title ) {
			textValue = value.getPrefixedText();
		}
		if( textValue ) {
			textValue = Ext.String.trim( textValue );
		}

		var record = this.findRecordByValue(textValue);
		if (!record || !record.isModel) {
			if( (skipLoad !== true) ) {
				//We have to manually unset the value because otherwise we'd
				//run into an infinite loop as "onload"
				//Ext.form.field.Combobox tries to set the (old) value again!
				me.value = null;
				me.store.load({
					params: {
						query: textValue
					},
					callback: function() {
						if (me.itemList) {
							me.itemList.unmask();
						}

						me.setValue(textValue, doSelect, true);
						me.autoSize();
						me.lastQuery = textValue;
					}
				});
				return false;
			}
		}

		return me.callParent([value, doSelect]);
	}
});