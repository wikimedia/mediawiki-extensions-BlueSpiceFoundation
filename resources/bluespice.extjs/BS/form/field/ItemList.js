Ext.define('BS.form.field.ItemList', {
	extend: 'Ext.form.FieldContainer',
	requires: [ 'Ext.ux.form.field.GridPicker' ],
	alias: ['widget.bs-itemlist'],
	baseCls: 'bs-form-field-itemlist',
	mixins: {
		field: 'Ext.form.field.Field'
	},
	layout: 'anchor',
	combineErrors: true,
	msgTarget: 'side',

	//model: null,
	apiFields: [],
	idProperty: 'id',
	inputDisplayField: 'text',
	listDisplayField: 'anchor',
	itemGridConfig: null,
	emptyText: mw.message( "bs-extjs-combo-box-default-placeholder" ).plain(),

	initComponent: function() {
		this.items = this.makeItems();

		this.callParent();
	},

	makeItems: function() {

		this.cbItemChooser = this.makeItemChooser();
		this.gdItems = this.makeItemGrid();

		return [
			this.cbItemChooser,
			this.gdItems
		];
	},

	makeItemChooser: function() {
		var me = this;
		//var combo = new Ext.ux.form.field.GridPicker({
		var combo = new Ext.form.field.ComboBox({
			emptyText: me.emptyText,
			anchor: '100%',
			displayField: this.inputDisplayField,
			listConfig: {
				getInnerTpl: function() {
					return '{["<span class=\'bs-icon-"+values.type+" bs-typeicon\'></span>"+values.' + me.inputDisplayField + ']}';
				}
			},

			/* TODO: Filter result to remove records that are already in the item list store */
			store: new Ext.data.JsonStore({
				proxy: {
					type: 'ajax',
					url: mw.util.wikiScript( 'api' ),
					reader: {
						type: 'json',
						root: 'results'
					},
					extraParams: {
						action: this.apiStore,
						format: 'json'
					}
				},
				//model: this.model, //for some reason having a string with the models class name does not work here...
				fields: this.apiFields,
				//groupField: 'type',
				remoteSort: true,
				autoLoad: true
			})
		});

		combo.on( 'select', this.onItemChooserSelect, this );

		return combo;
	},

	makeItemGrid: function() {

		var deleteCol = new Ext.grid.column.Action({
			width: 30,
			sortable: false,
			menuDisabled: true,
			items: [{
				iconCls: 'icon-cross3',
				glyph: true, //Needed to have the "BS.override.grid.column.Action" render an <span> instead of an <img>
				scope: this,
				handler: this.onRemoveClick
			}]
		});

		var grid = new Ext.grid.Panel( Ext.merge({
				hideHeaders: true,
				selModel: {
					mode: 'MULTI'
				},
				columns: [
					{
						dataIndex: 'type',
						width: 30,
						renderer: function( value ) {
							return mw.html.element( 'span', {
								class: 'bs-icon-' + value
							} );
						}
					},
					{
						dataIndex: this.listDisplayField,
						flex: 1
					},
					deleteCol
				],
				width: '100%',
				store: this.makeItemGridStore()
			},
			this.itemGridConfig
		));

		grid.on( 'cellkeydown', this.checkDelKey, this );

		return grid;
	},

	makeItemGridStore: function() {
		return new Ext.create( 'Ext.data.Store', {
			fields: this.apiFields,
			data:{ 'items':[] },
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					root: 'items',
				}
			}
		});
	},

	onItemChooserSelect: function( combo, records, eOpts ) {
		this.addToList( records );
		combo.clearValue();
	},

	makeFindByIdFunction: function( currentRecord ) {
		var me = this;
		return function( record, id ) {
			if( record.get( me.idProperty ) === currentRecord.get( me.idProperty ) ) {
				return true;
			}
			return false;
		};
	},

	addToList: function( records ) {
		for( var i = 0; i < records.length; i++ ) {
			var idx = this.gdItems.getStore().findBy(
				this.makeFindByIdFunction( records[i] )
			);

			if( idx === -1 ) {
				this.gdItems.getStore().add( records[i] );
			}
		}
	},

	checkDelKey: function( grid, td, cellIndex, record, tr, rowIndex, e, eOpts ) {
		if ( e.getKey() === e.DELETE ) {
			var selectedRows = grid.getSelectionModel().getSelection();
			grid.getStore().remove( selectedRows );
		}
	},

	onRemoveClick: function( view, rowIndex, colIndex, item, e, record, row ) {
		view.getStore().removeAt( rowIndex );
	},

	getValue:function() {
		var records = this.gdItems.getStore().getRange();
		var data = [];

		for( var i = 0; i < records.length; i++ ) {
			data.push( records[i].data );
		}
		return data;
	},

	setValue: function( data ) {
		data = data || {};
		this.gdItems.getStore().loadData( data );
	}
});