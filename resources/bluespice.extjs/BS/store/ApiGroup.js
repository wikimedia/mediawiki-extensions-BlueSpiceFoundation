Ext.define( 'BS.store.ApiGroup', {
	extend: 'BS.store.BSApi',
	apiAction: 'bs-group-store',
	fields: [
		'group_name',
		'additional_group',
		'group_type',
		'displayname'
	],
	filters: [ {
		property: 'group_type',
		type: 'list',
		value: [ 'custom', 'core-minimal', 'extension-minimal' ]
	} ]
} );
