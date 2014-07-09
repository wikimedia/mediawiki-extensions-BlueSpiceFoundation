Ext.define( 'BS.Panel', {
	extend: 'Ext.Panel',
	layout: 'border',
	fieldDefaults: {
		labelAlign: 'right'
	},
	bodyPadding:5,

	constructor: function() {
		//Custom Settings
		this.currentData = {};
		this.callParent(arguments);
	},
	
	initComponent: function() {
		
		this.afterInitComponent( arguments );

		this.callParent( arguments );
	},
	
	afterInitComponent: function() {
		
	},
	
	getData: function(){
		return this.currentData;
	},

	setData: function( obj ){
		this.currentData = obj;
	}
});