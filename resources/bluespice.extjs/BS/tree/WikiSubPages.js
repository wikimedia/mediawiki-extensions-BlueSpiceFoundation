Ext.define( 'BS.tree.WikiSubPages', {
	extend: 'Ext.tree.Panel',
	requires: [ 'BS.model.Title' ],
	rootVisible: false,
	useArrows: true,

	treeRootPath: '',

	initComponent: function() {
		this.store = new Ext.data.TreeStore( {
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript( 'api' ),
				extraParams: {
					format: 'json',
					action: 'bs-wikisubpage-treestore'
				}
			},
			root: {
				text: 'root',
				id: this.treeRootPath,
				expanded: true
			},
			folderSort: true/*,
			model: 'BS.model.Title'*/
		});

		this.callParent( arguments );
	}
});