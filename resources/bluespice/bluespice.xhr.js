//Ext.ns('Ext.BlueSpice', 'BsXHRResponseStatus');
BsXHRResponseStatus = {
	SUCCESS: 'success',
	ERROR: 'error'
};

//Ext.ns('Ext.BlueSpice', 'BsXHRResponse');
BsXHRResponse = {
	newFromExtJSResponseObject: function( oResponse ) {
		return Ext.decode( oResponse.responseText );
	}
};