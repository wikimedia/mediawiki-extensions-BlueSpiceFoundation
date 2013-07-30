( function ( mw, bs, $, undefined ) {
	"use strict";
	
	Ext4.Loader.setPath( 'BS', wgScriptPath+'/extensions/BlueSpiceFoundation/resources/bluespice.extjs/BS');
	//Ext4.Loader.setPath( 'BS.WikiAdmin', wgScriptPath+'/extensions/SomeWhere');
	
	function newTitleStore() {
		return {};
	}
	
	function newUserStore() {
		return {};
	}
	
	function newCategoryTreeStore() {
		return {};
	}
	
	var extjs = {
		newTitleStore: newTitleStore,
		newUserStore: newUserStore,
		newCategoryTreeStore: newCategoryTreeStore
	}
	
	bs.extjs = extjs;
	
}( mediaWiki, blueSpice, jQuery ) );