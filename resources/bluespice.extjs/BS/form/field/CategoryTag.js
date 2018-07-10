Ext.define( 'BS.form.field.CategoryTag', {
	extend:'Ext.form.field.Tag',
	requires: [ 'BS.model.Category', 'BS.tree.Categories' ],
	displayField: 'text',
	valueField: 'text',
	anchor: '95%',
	pinList: true,
	triggerOnClick: true,
	triggerAction: 'all',
	filterPickList: true,
	forceSelection: false,
	createNewOnEnter: true,
	emptyText: mw.message('bs-extjs-categoryboxselect-emptytext').plain(),
	delimiter: '|',

	deferredSetValueConf: false,
	showTreeTrigger: true,

	constructor: function( cfg ) {
		this.callParent( [cfg] );

		if( this.showTreeTrigger ) {
			var triggers = this.getTriggers();
			triggers.showTree = new Ext.form.trigger.Trigger({
				cls : Ext.baseCSSPrefix + 'form-search-trigger bs-form-tree-trigger',
				handler: this.showTree
			});
			this.setTriggers( triggers );
		}
	},

	initComponent: function() {
		this.store = Ext.create( 'Ext.data.JsonStore', {
			proxy: {
				type: 'ajax',
				url: bs.api.makeUrl( 'bs-category-store' ),
				reader: {
					type: 'json',
					rootProperty: 'results',
					idProperty: 'cat_id'
				},
				extraParams: {
					limit: 9999
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
		/*if( this.store.isLoading() ) {
			this.deferSetValue( this.setValueByNames, names );
			return;
		}
		var indexes = [];
		Ext.each( names, function( name, idx ){
			var index = this.store.find( 'cat_title', name );
			var record = this.store.getAt( index );
			indexes.push( record.get( 'cat_id' ) );
		}, this );

		this.setValue( indexes.join(',') + "" );*/
	},
	deferSetValue: function( callback, value ) {
		this.deferredSetValueConf = {
			callback: callback,
			value: value
		};
	},

	onTrigger2Click : function( event ){
		//lazy loading, as this trigger is optional
		Ext.require( 'BS.tree.Categories', this.showTree, this );
	},

	wdTree: null,
	showTree: function() {
		if( !this.wdTree ) {
			var categoryTree =  new BS.tree.Categories({
				width: 250,
				height: 300
			});
			categoryTree.on( 'itemclick', this.onTreeItemClick, this );

			this.wdTree = new Ext.Window({
				modal: true,
				title: mw.message('bs-extjs-categorytree-title').plain(),
				x: this.getX() + this.getWidth() + 10,
				y: this.getY() + 50,
				closeAction: 'hide',
				items: [
					categoryTree
				]
			});

			this.wireUpWithContainerWindow();
		}

		this.wdTree.show();
	},

	onTreeItemClick: function( tree, record, item, index, e, eOpts ) {
		if ( mw.config.get( 'BSInsertCategoryWithParents' ) ) {
			this.addValuesFromRecord( record );
		}
		else {
			this.addValue( [ record.data.text ] );
		}
	},

	addValuesFromRecord: function ( record ) {
		//parentNode is null if there is no parent, internalId "src" is the root of the categories
		if ( record.parentNode && record.parentNode.internalId !== "src" ) {
			this.addValuesFromRecord( record.parentNode );
		}
		this.addValue( [ record.data.text ] );
	},

	/**
	 * This is a little bit tricky. If our CategoryBoxSelect field is within a window we need to close the tree
	 * window when the parent window closes. As the CategoryBoxSelect is not necessarily in a window we need some
	 * checks here
	 */
	wireUpWithContainerWindow: function() {
		var parentWindow = this.up( 'window' );
		if( !parentWindow ) {
			return;
		}

		parentWindow.on( 'close', function() {
			this.wdTree.close();
		}, this );
	}
});
