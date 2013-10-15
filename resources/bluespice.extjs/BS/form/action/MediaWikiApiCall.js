Ext.define('BS.form.action.MediaWikiApiCall', {
	extend: 'Ext.form.action.Submit',
	createCallback: function() {
		var callbackCfg = this.callParent(arguments);
		callbackCfg.success = this.success;
		callbackCfg.failure = this.failure;
		callbackCfg.scope   = this.scope;
		return callbackCfg;
	}
});