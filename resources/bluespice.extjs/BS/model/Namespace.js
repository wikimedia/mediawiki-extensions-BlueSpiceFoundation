Ext.define('BS.model.Namespace', {
	extend: 'Ext.data.Model',

	fields: [
		{ name: 'namespaceId', type: 'int' },
		{ name: 'namespaceName', type: 'string' },
		{ name: 'isNonincludable', type: 'bool' },
		{ name: 'namespaceContentModel', type: 'string' }
	],
	/*
	proxy: {
		type: 'ajax',
		url: 'app/data/namespaces.json',
		reader: {
			model: 'BS.model.Namespace',
			type: 'json',
			root: 'namespaces',
			idProperty: 'namespaceId'
		}
	},*/
	
	getNamespaceContentModel: function() {
		return this.get('namespaceContentModel');
	},
	
	isNonincludable: function(){
		return this.get('isNonincludable');
	},

	isSubject: function() {
		return !this.isTalk();
	},
	
	/**
	 * Is the current namespace a talk namespace?
	 *
	 * @return bool
	 */
	isTalk: function() {
		//TODO: replace 0 with somethin similar to NS_MAIN
		return this.get('namespaceId') > 0 && this.get('namespaceId') % 2;
	}
});
