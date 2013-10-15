Ext.define( 'BS.Panel', {
	extend: 'Ext.Panel',
	layout: 'border',
	fieldDefaults: {
		labelAlign: 'right'
	},
	bodyPadding:5,

	//Custom Setting
	currentData: {},
	
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