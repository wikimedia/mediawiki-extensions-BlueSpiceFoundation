Ext.define( 'BS.store.ApiGroup', {
	extend: 'BS.store.BSApi',
	apiAction: 'bs-group-store',
	fields: [
		'group_name',
		'additional_group',
		'displayname'
	]
} );
