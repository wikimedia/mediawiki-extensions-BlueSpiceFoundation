Ext.define( 'BS.tree.Categories', {
	extend: 'Ext.tree.Panel',
	requires: [ 'BS.model.Category' ],
	useArrows: true,
	rootVisible: false,
	displayField: 'text',

	initComponent: function() {
		this.store = new Ext.data.TreeStore({
			proxy: {
				type: 'ajax',
				url: bs.api.makeUrl( 'bs-category-treestore' )
			},
			defaultRootProperty: 'results',
			root: {
				text: 'Categories',
				id: 'src',
				expanded: true
			},
			model: 'BS.model.Category'
		});

		this.callParent( arguments );
	}
});