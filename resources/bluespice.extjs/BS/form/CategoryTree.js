BsCategoryChooser = Ext.extend(Ext.form.Field,  {
	width:400,
	height:300,
	cls: 'bs-form-categorychooser',
	defaultAutoCreate : {tag: "div"},
	initComponent: function(){
		BsCategoryChooser.superclass.initComponent.call(this);

		this.chosenCategoriesStore = new Ext.data.JsonStore({
			fields: [
				{name: 'text'},
			]
		});
	},

	// private
	onRender: function(ct, position){
		BsCategoryChooser.superclass.onRender.call(this, ct, position);

		var fs = this.fs = new Ext.form.FieldSet({
			renderTo: this.el,
			title: this.legend,
			height: this.height,
			width: this.width,
			style: "padding:0;",
			tbar: this.tbar
		});
		fs.body.addClass('bs-catchooser');

		this.chosenCategoriesGrid = new Ext.grid.GridPanel({
			border: false,
			height: this.height - 2 * 65,
			width: this.width / 2,
			store: this.chosenCategoriesStore,
			columns: [
				{
					id       :'category',
					dataIndex: 'text'
				},
				{
					xtype: 'actioncolumn',
					width: 25,
					items: [{
						icon   : stylepath+'/bluespice/bs-delete.png',  // Use a URL in the icon config
						tooltip: '',
						handler: this.removeCategoryClicked,
						scope: this
					}]
				}
			],
			stripeRows: true,
			enableHdMenu: false,
			hideHeaders: true,
			autoExpandColumn: 'category'
		});

		this.availableCategoriesTree = new Ext.tree.TreePanel({
			border: false,
			store: this.store,
			columns: [{ header: 'Value', width: 1, dataIndex: this.displayField }],
			hideHeaders: true,
			height: this.height - 2 * 65,
			width: this.width / 2,
			autoScroll: true,
			dataUrl: BsCore.buildRemoteString('InsertCategory', 'getCategory'),
			singleExpand: false,
			root: {
				nodeType: 'async',
				text: 'root',
				draggable: false,
				id: 'root',
				expanded: true
			},
			rootVisible: false
		});
		this.availableCategoriesTree.addListener( 'dblclick', this.nodeClicked, this );

		this.descTreePanel = new Ext.Panel({
			colspan: 2,
			border: false,
			bodyCssClass: 'bs_description_class',
			html: '<p><i>' + BsCategoryChooserI18N.descAddCategory + '</i></p>',
			height: 60,
			width: this.width
		});

		this.descAddCategory = new Ext.Panel({
			border: false,
			bodyCssClass: 'bs_description_class',
			html: '<p><i>' + BsCategoryChooserI18N.descTreePanel + '</i></p>',
			height: 60,
			width: this.width / 2
		});

		this.btnAddCategory = new Ext.Button({
			xtype: 'button',
			id: 'bs-ic-new-category-button',
			text: BsCategoryChooserI18N.textBtnAdd
		});
		this.btnAddCategory.addListener( 'click', this.btnAddCategoryClicked, this );
		
		this.tfNewCategory = new Ext.form.TextField({
			xtype: 'textfield',
			id: 'bs-ic-new-category-textfield',
			width: 100
		});

		this.panelAddCategory = new Ext.FormPanel({
			border: false,
			height: 60,
			layout: 'table',
			layoutConfig: {
				columns: 2
			},
			bodyCssClass: 'bs_description_class',
			padding: 5,
			items: [
				new Ext.form.Label({
					text: BsCategoryChooserI18N.labelAddCategory,
					colspan:2
				}),
				this.tfNewCategory,
				this.btnAddCategory
			]
		});

		this.view = new Ext.Panel({
			border: false,
			layout:"table",
			layoutConfig: {
				columns: 2
			},
			items:[
				this.availableCategoriesTree,
				this.chosenCategoriesGrid,
				this.descAddCategory,
				this.panelAddCategory,
				this.descTreePanel
			]
		});

		fs.add(this.view);
		fs.doLayout();
	},
	
	btnAddCategoryClicked: function( btn, event ) {
		var textValue = this.tfNewCategory.getValue().replace(' ', '_');
		if( textValue === '' ) return;
		var existingIndex = this.chosenCategoriesStore.findExact( 'text', textValue );
		if ( existingIndex !== -1 ) {
			this.tfNewCategory.setValue('');
			return; //Prevent duplicates
		}

		var newIndex = this.chosenCategoriesStore.getCount();
		var newRecordData = { 
			text: textValue
		};
		var newRecord = new this.chosenCategoriesStore.recordType( newRecordData, newIndex );
		this.chosenCategoriesStore.add(newRecord);
		this.tfNewCategory.setValue('');
	},
	
	removeCategoryClicked: function( grid, index, noIdeaWhatThisParamIs, btn, event ) {
		this.chosenCategoriesStore.removeAt(index);
	},
	
	nodeClicked: function( node, event ) {
		var existingIndex = this.chosenCategoriesStore.findExact( 
			'text', node.attributes.text.replace('_', ' ')
		);
		if ( existingIndex !== -1 ) {
			return; //Prevent duplicates
		}

		var newIndex = this.chosenCategoriesStore.getCount();
		var newRecordData = { 
			text: node.attributes.text
		};
		var newRecord = new this.chosenCategoriesStore.recordType( newRecordData, newIndex );
		this.chosenCategoriesStore.add(newRecord);
		this.availableCategoriesTree.getSelectionModel().clearSelections();
	},

	getValue: function(valueField){
		return this.chosenCategoriesStore.getRange();
	},

	setValue: function(values) {
		this.chosenCategoriesStore.loadData(values);
	},

	reset : function() {
		this.setValue('');
		this.tfNewCategory.setValue('');
		this.availableCategoriesTree.getSelectionModel().clearSelections();
		this.availableCategoriesTree.collapseAll();
	},

	getRawValue: function(valueField) {

	},

	setRawValue: function(values){
		setValue(values);
	},

	validateValue : function(value){

	}
});